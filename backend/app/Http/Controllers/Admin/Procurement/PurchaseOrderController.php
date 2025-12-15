<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderDetail;
use App\Models\Procurement\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['status', 'supplier', 'purchaseRequest', 'creator']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('PONumber', 'like', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('POStatusID', $request->status);
        }

        $sortBy = $request->get('sort_by', 'CreatedDate');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'PONumber' => 'required|string|unique:purchaseorder,PONumber',
            'PurchaseRequestID' => 'required|exists:purchaserequest,PurchaseRequestID',
            'SupplierID' => 'required|exists:supplier,SupplierID',
            'PODate' => 'required|date',
            'ModeOfProcurement' => 'nullable|string',
            'PlaceOfDelivery' => 'nullable|string',
            'DateOfDelivery' => 'nullable|string',
            'DeliveryTerm' => 'nullable|string',
            'PaymentTerm' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ItemDescription' => 'required|string',
            'items.*.Quantity' => 'required|numeric|min:1',
            'items.*.UnitCost' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify PR is Approved
        $pr = PurchaseRequest::find($request->PurchaseRequestID);
        if ($pr->PRStatusID !== 2) { // 2 = Approved
            return response()->json(['message' => 'Purchase Request must be Approved to create a PO'], 400);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += ($item['Quantity'] * $item['UnitCost']);
            }

            $po = PurchaseOrder::create([
                'PONumber' => $request->PONumber,
                'PurchaseRequestID' => $request->PurchaseRequestID,
                'SupplierID' => $request->SupplierID,
                'POStatusID' => 1, // Pending
                'PODate' => $request->PODate,
                'ModeOfProcurement' => $request->ModeOfProcurement,
                'PlaceOfDelivery' => $request->PlaceOfDelivery,
                'DateOfDelivery' => $request->DateOfDelivery,
                'DeliveryTerm' => $request->DeliveryTerm,
                'PaymentTerm' => $request->PaymentTerm,
                'TotalAmount' => $totalAmount,
                'Remarks' => $request->Remarks,
                'CreatedBy' => Auth::id(),
                'CreatedDate' => now(),
                'UpdatedDate' => now(),
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderDetail::create([
                    'PurchaseOrderID' => $po->PurchaseOrderID,
                    'ItemDescription' => $item['ItemDescription'],
                    'Quantity' => $item['Quantity'],
                    'UnitCost' => $item['UnitCost'],
                    'TotalCost' => $item['Quantity'] * $item['UnitCost'],
                    'Remarks' => $item['Remarks'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase Order created successfully',
                'data' => $po->load('details')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create Purchase Order', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $po = PurchaseOrder::with(['status', 'supplier', 'purchaseRequest', 'details', 'creator'])->find($id);

        if (!$po) {
            return response()->json(['message' => 'Purchase Order not found'], 404);
        }

        return response()->json($po);
    }

    public function update(Request $request, $id)
    {
        $po = PurchaseOrder::find($id);

        if (!$po) {
            return response()->json(['message' => 'Purchase Order not found'], 404);
        }

        if ($po->POStatusID !== 1) {
            return response()->json(['message' => 'Cannot edit Purchase Order that is not Pending'], 403);
        }

        $validator = Validator::make($request->all(), [
            'SupplierID' => 'sometimes|exists:supplier,SupplierID',
            'PODate' => 'sometimes|date',
            'ModeOfProcurement' => 'nullable|string',
            'PlaceOfDelivery' => 'nullable|string',
            'DateOfDelivery' => 'nullable|string',
            'DeliveryTerm' => 'nullable|string',
            'PaymentTerm' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.ItemDescription' => 'required_with:items|string',
            'items.*.Quantity' => 'required_with:items|numeric|min:1',
            'items.*.UnitCost' => 'required_with:items|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $po->update([
                'SupplierID' => $request->SupplierID ?? $po->SupplierID,
                'PODate' => $request->PODate ?? $po->PODate,
                'Remarks' => $request->Remarks ?? $po->Remarks,
                'ModeOfProcurement' => $request->ModeOfProcurement ?? $po->ModeOfProcurement,
                'PlaceOfDelivery' => $request->PlaceOfDelivery ?? $po->PlaceOfDelivery,
                'DateOfDelivery' => $request->DateOfDelivery ?? $po->DateOfDelivery,
                'DeliveryTerm' => $request->DeliveryTerm ?? $po->DeliveryTerm,
                'PaymentTerm' => $request->PaymentTerm ?? $po->PaymentTerm,
                'UpdatedDate' => now(),
            ]);

            if ($request->has('items')) {
                // Delete old details
                PurchaseOrderDetail::where('PurchaseOrderID', $po->PurchaseOrderID)->delete();

                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $totalAmount += ($item['Quantity'] * $item['UnitCost']);
                    PurchaseOrderDetail::create([
                        'PurchaseOrderID' => $po->PurchaseOrderID,
                        'ItemDescription' => $item['ItemDescription'],
                        'Quantity' => $item['Quantity'],
                        'UnitCost' => $item['UnitCost'],
                        'TotalCost' => $item['Quantity'] * $item['UnitCost'],
                        'Remarks' => $item['Remarks'] ?? null,
                    ]);
                }
                $po->update(['TotalAmount' => $totalAmount]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase Order updated successfully',
                'data' => $po->load('details')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update Purchase Order', 'error' => $e->getMessage()], 500);
        }
    }

    public function approve($id)
    {
        $po = PurchaseOrder::find($id);
        if (!$po) return response()->json(['message' => 'PO not found'], 404);

        $po->update(['POStatusID' => 2, 'UpdatedDate' => now()]); // 2 = Approved/Open
        return response()->json(['message' => 'Purchase Order approved']);
    }

    public function reject($id)
    {
        $po = PurchaseOrder::find($id);
        if (!$po) return response()->json(['message' => 'PO not found'], 404);

        $po->update(['POStatusID' => 3, 'UpdatedDate' => now()]); // 3 = Rejected
        return response()->json(['message' => 'Purchase Order rejected']);
    }
}
