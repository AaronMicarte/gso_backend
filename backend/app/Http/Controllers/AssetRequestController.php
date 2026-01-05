<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetAuditLog;
use App\Models\PropertyInventoryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssetRequestController extends Controller
{
    /**
     * Get all asset requests (with filters for different roles)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AssetRequest::with([
            'requester:UserID,FirstName,LastName,Email',
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'details.inventoryDetail.propertyType:PropertyTypeID,PropertyTypeName',
            'reviewer:UserID,FirstName,LastName',
            'issuer:UserID,FirstName,LastName'
        ]);

        // Role-based filtering
        if ($user->RoleID === 3) { // Department Head
            $query->where('DepartmentID', $user->DepartmentID);
        } elseif ($user->RoleID === 2) { // Staff (can only see their own)
            $query->where('RequestedBy', $user->UserID);
        }
        // Admin/GSO can see all

        // Status filter
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('RequestDate', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('RequestDate', '<=', $request->to_date);
        }

        $requests = $query->orderBy('RequestDate', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($requests);
    }

    /**
     * Get single asset request
     */
    public function show($id)
    {
        $request = AssetRequest::with([
            'requester:UserID,FirstName,LastName,Email,DepartmentID',
            'requester.department:DepartmentID,DepartmentName',
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'details.inventoryDetail.propertyType:PropertyTypeID,PropertyTypeName',
            'details.inventoryDetail.propertyInventory:InventoryID,ItemDescription,Brand,ModelNumber,SerialNumber',
            'details.inventoryDetail.condition:ConditionID,ConditionName',
            'reviewer:UserID,FirstName,LastName',
            'issuer:UserID,FirstName,LastName',
            'issuances',
            'auditLogs.performer:UserID,FirstName,LastName'
        ])->findOrFail($id);

        return response()->json($request);
    }

    /**
     * Create new asset request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.PropertyInventoryDetailID' => 'nullable|exists:propertyinventorydetail,InventoryDetailID',
            'items.*.ItemDescription' => 'required_without:items.*.PropertyInventoryDetailID|nullable|string|max:255',
            'items.*.Unit' => 'nullable|string|max:50',
            'items.*.QuantityRequested' => 'required|integer|min:1',
            'items.*.Purpose' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();

        // Validate inventory availability ONLY for inventory items
        foreach ($validated['items'] as $item) {
            if (!empty($item['PropertyInventoryDetailID'])) {
                $inventoryDetail = PropertyInventoryDetail::with('propertyInventory')->findOrFail($item['PropertyInventoryDetailID']);
                if ($inventoryDetail->QuantityOnHand < $item['QuantityRequested']) {
                    return response()->json([
                        'message' => 'Insufficient quantity available for item: ' . ($inventoryDetail->propertyInventory->ItemDescription ?? 'Unknown Item'),
                        'available' => $inventoryDetail->QuantityOnHand,
                        'requested' => $item['QuantityRequested']
                    ], 422);
                }
            }
        }

        DB::beginTransaction();
        try {
            $assetRequest = AssetRequest::create([
                'RequestNumber' => AssetRequest::generateRequestNumber(),
                'RequestedBy' => $user->UserID,
                'DepartmentID' => $user->DepartmentID,
                'Status' => 'pending',
                'RequestDate' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $assetRequest->details()->create([
                    'PropertyInventoryDetailID' => $item['PropertyInventoryDetailID'] ?? null,
                    'ItemDescription' => $item['ItemDescription'] ?? null,
                    'Unit' => $item['Unit'] ?? null,
                    'QuantityRequested' => $item['QuantityRequested'],
                    'Purpose' => $item['Purpose'],
                ]);
            }

            // Create audit log
            AssetAuditLog::createLog(
                'assetrequest',
                $assetRequest->RequestID,
                'created',
                $user->UserID,
                [
                    'request_number' => $assetRequest->RequestNumber,
                    'items_count' => count($validated['items']),
                    'department' => $user->department->DepartmentName ?? 'Unknown'
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset request created successfully',
                'request' => $assetRequest->load(['requester', 'department', 'details.inventoryDetail'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create asset request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve asset request (Property Officer only)
     */
    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'ReviewNotes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check authorization (Admin or specific role)
        if (!in_array($user->RoleID, [1, 4])) { // Admin or Property Officer
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assetRequest = AssetRequest::findOrFail($id);

        if (!$assetRequest->canBeApproved()) {
            return response()->json([
                'message' => 'Request cannot be approved',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $assetRequest->update([
                'Status' => 'approved',
                'ReviewedBy' => $user->UserID,
                'ReviewedAt' => now(),
                'ReviewNotes' => $validated['ReviewNotes'] ?? null,
            ]);

            // Create audit log
            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'approved',
                $user->UserID,
                [
                    'request_number' => $assetRequest->RequestNumber,
                    'notes' => $validated['ReviewNotes'] ?? null
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset request approved successfully',
                'request' => $assetRequest->load(['reviewer'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to approve request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject asset request (Property Officer only)
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'ReviewNotes' => 'required|string|min:10|max:500',
        ]);

        $user = Auth::user();

        if (!in_array($user->RoleID, [1, 4])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assetRequest = AssetRequest::findOrFail($id);

        if (!$assetRequest->canBeApproved()) {
            return response()->json([
                'message' => 'Request cannot be rejected',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $assetRequest->update([
                'Status' => 'rejected',
                'ReviewedBy' => $user->UserID,
                'ReviewedAt' => now(),
                'ReviewNotes' => $validated['ReviewNotes'],
            ]);

            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'rejected',
                $user->UserID,
                [
                    'request_number' => $assetRequest->RequestNumber,
                    'reason' => $validated['ReviewNotes']
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset request rejected',
                'request' => $assetRequest->load(['reviewer'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reject request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel asset request (Requester only, if pending/approved)
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $assetRequest = AssetRequest::findOrFail($id);

        // Check if user owns this request
        if ($assetRequest->RequestedBy !== $user->UserID) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$assetRequest->canBeCancelled()) {
            return response()->json([
                'message' => 'Request cannot be cancelled',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $assetRequest->update(['Status' => 'cancelled']);

            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'cancelled',
                $user->UserID,
                ['request_number' => $assetRequest->RequestNumber]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset request cancelled successfully',
                'request' => $assetRequest
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available inventory items for request
     */
    public function getAvailableInventory()
    {
        $items = PropertyInventoryDetail::with([
            'propertyInventory:InventoryID,ItemDescription,Brand,ModelNumber,SerialNumber',
            'propertyType:PropertyTypeID,PropertyTypeName,PropertyClassificationID',
            'propertyType.propertyClassification:PropertyClassificationID,ClassificationName,DocumentType',
            'condition:ConditionID,ConditionName',
            'unitOfMeasure:UnitOfMeasureID,UnitName'
        ])
        ->where('QuantityOnHand', '>', 0)
        ->where('ConditionID', '!=', 4) // Exclude unserviceable items
        ->orderBy('InventoryDetailID', 'desc')
        ->get();

        return response()->json($items);
    }
}
