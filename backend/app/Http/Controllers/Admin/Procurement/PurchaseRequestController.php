<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\PurchaseRequest;
use App\Models\Procurement\PurchaseRequestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['status', 'requester', 'department', 'details']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('PRNumber', 'like', "%{$search}%")
                    ->orWhere('Purpose', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($request->has('status')) {
            $query->where('PRStatusID', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'CreatedDate');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Purpose' => 'required|string',
            'DepartmentID' => 'required|exists:department,DepartmentID',
            'items' => 'required|array|min:1',
            'items.*.ItemDescription' => 'required|string',
            'items.*.Quantity' => 'required|integer|min:1',
            'items.*.UnitCost' => 'required|numeric|min:0',
            'items.*.UnitOfMeasureID' => 'required|exists:unitofmeasure,UnitOfMeasureID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $currentUser = Auth::user();

            // CRITICAL VALIDATION: User can only create PR for their own department
            if ($request->DepartmentID != $currentUser->DepartmentID) {
                return response()->json([
                    'message' => 'Security Error: You can only create Purchase Requests for your own department',
                    'your_department_id' => $currentUser->DepartmentID,
                    'attempted_department_id' => $request->DepartmentID
                ], 403);
            }

            // Generate PR Number automatically
            $year = date('Y');
            $lastPR = PurchaseRequest::whereYear('DateRequested', $year)
                ->orderBy('PurchaseRequestID', 'desc')
                ->first();

            $nextNumber = $lastPR ? intval(substr($lastPR->PRNumber, -4)) + 1 : 1;
            $prNumber = sprintf('PR-%s-%04d', $year, $nextNumber);

            $pr = PurchaseRequest::create([
                'PRNumber' => $prNumber,
                'DateRequested' => now()->toDateString(), // Always today's date
                'Purpose' => $request->Purpose,
                'DepartmentID' => $currentUser->DepartmentID, // Use user's department (not request payload)
                'RequestedBy' => Auth::id(), // Current user
                'PRStatusID' => 1, // Pending
                'CreatedDate' => now(),
            ]);

            foreach ($request->items as $item) {
                PurchaseRequestDetail::create([
                    'PurchaseRequestID' => $pr->PurchaseRequestID,
                    'ItemDescription' => $item['ItemDescription'],
                    'UnitOfMeasureID' => $item['UnitOfMeasureID'],
                    'QuantityRequested' => $item['Quantity'],
                    'EstimatedUnitCost' => $item['UnitCost'],
                    'EstimatedTotalCost' => $item['Quantity'] * $item['UnitCost'],
                    'Remarks' => $item['Remarks'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase Request created successfully',
                'data' => $pr->load('details')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create Purchase Request', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $pr = PurchaseRequest::with(['status', 'requester', 'department', 'details', 'approver'])->find($id);

        if (!$pr) {
            return response()->json(['message' => 'Purchase Request not found'], 404);
        }

        return response()->json($pr);
    }

    public function update(Request $request, $id)
    {
        $pr = PurchaseRequest::find($id);

        if (!$pr) {
            return response()->json(['message' => 'Purchase Request not found'], 404);
        }

        // Only allow editing if Pending
        if ($pr->PRStatusID !== 1) {
            return response()->json(['message' => 'Cannot edit Purchase Request that is not Pending'], 403);
        }

        $validator = Validator::make($request->all(), [
            'Purpose' => 'required|string',
            'DepartmentID' => 'required|exists:department,DepartmentID',
            'items' => 'required|array|min:1',
            'items.*.ItemDescription' => 'required|string',
            'items.*.Quantity' => 'required|integer|min:1',
            'items.*.UnitCost' => 'required|numeric|min:0',
            'items.*.UnitOfMeasureID' => 'required|exists:unitofmeasure,UnitOfMeasureID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $pr->update([
                'Purpose' => $request->Purpose,
                'DepartmentID' => $request->DepartmentID,
                'UpdatedDate' => now(),
            ]);

            // Replace items (simplest approach for now)
            PurchaseRequestDetail::where('PurchaseRequestID', $id)->delete();

            foreach ($request->items as $item) {
                PurchaseRequestDetail::create([
                    'PurchaseRequestID' => $pr->PurchaseRequestID,
                    'ItemDescription' => $item['ItemDescription'],
                    'UnitOfMeasureID' => $item['UnitOfMeasureID'],
                    'QuantityRequested' => $item['Quantity'],
                    'EstimatedUnitCost' => $item['UnitCost'],
                    'EstimatedTotalCost' => $item['Quantity'] * $item['UnitCost'],
                    'Remarks' => $item['Remarks'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase Request updated successfully',
                'data' => $pr->load('details')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update Purchase Request', 'error' => $e->getMessage()], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        $pr = PurchaseRequest::find($id);

        if (!$pr) {
            return response()->json(['message' => 'Purchase Request not found'], 404);
        }

        $pr->update([
            'PRStatusID' => 2, // Approved
            'ApprovedBy' => Auth::id(),
            'DateApproved' => now(),
            'UpdatedDate' => now(),
        ]);

        return response()->json(['message' => 'Purchase Request approved successfully']);
    }

    public function reject(Request $request, $id)
    {
        $pr = PurchaseRequest::find($id);

        if (!$pr) {
            return response()->json(['message' => 'Purchase Request not found'], 404);
        }

        $pr->update([
            'PRStatusID' => 3, // Rejected
            'UpdatedDate' => now(),
        ]);

        return response()->json(['message' => 'Purchase Request rejected successfully']);
    }
}
