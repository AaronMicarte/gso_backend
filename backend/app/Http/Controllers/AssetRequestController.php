<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetAuditLog;
use App\Models\Inventory\PropertyInventoryDetail;
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
            'requester:UserID,FullName,Email',
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'details.inventoryDetail',
            'reviewer:UserID,FullName',
            'issuer:UserID,FullName'
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
            'requester:UserID,FullName,Email,DepartmentID',
            'requester.department:DepartmentID,DepartmentName',
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'details.inventoryDetail.propertyType:PropertyTypeID,PropertyTypeName',
            'details.inventoryDetail.propertyInventory:InventoryID,ItemDescription,Brand,ModelNumber,SerialNumber',
            'details.inventoryDetail.condition:ConditionID,ConditionName',
            'reviewer:UserID,FullName',
            'issuer:UserID,FullName',
            'auditLogs.performer'
        ])->findOrFail($id);

        return response()->json($request);
    }

    /**
     * Create new asset request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Purpose' => 'required|string|min:10|max:1000',
            'items' => 'required|array|min:1',
            'items.*.PropertyInventoryDetailID' => 'nullable|exists:propertyinventorydetail,InventoryDetailID',
            'items.*.ItemDescription' => 'required|string|max:255',
            'items.*.Unit' => 'nullable|string|max:50',
            'items.*.QuantityRequested' => 'required|integer|min:1',
            'items.*.Purpose' => 'required|string|min:5|max:1000',
            'items.*.IntendedEndUser' => 'nullable|string|max:255',
            'items.*.AdditionalNotes' => 'nullable|string|max:1000',
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
                'Purpose' => $validated['Purpose'],
                'Status' => 'pending',
                'RequestDate' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $assetRequest->details()->create([
                    'PropertyInventoryDetailID' => $item['PropertyInventoryDetailID'] ?? null,
                    'ItemDescription' => $item['ItemDescription'],
                    'Unit' => $item['Unit'] ?? null,
                    'QuantityRequested' => $item['QuantityRequested'],
                    'Purpose' => $item['Purpose'],
                    'IntendedEndUser' => $item['IntendedEndUser'] ?? null,
                    'AdditionalNotes' => $item['AdditionalNotes'] ?? null,
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
                'request' => $assetRequest->load(['requester', 'department', 'details'])
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
            'details' => 'nullable|array',
            'details.*.RequestDetailID' => 'required|integer',
            'details.*.PropertyClassification' => 'required|in:ICS,PAR',
            'details.*.AssignedEndUserID' => 'nullable|integer',
            'details.*.AssignedDepartmentID' => 'nullable|integer',
            'details.*.AdminNotes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check authorization (Admin or Property Officer)
        if (!in_array($user->UserRoleID, [1, 2])) { // Admin or GSO Staff
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
            // Update review data for each item
            if (isset($validated['details'])) {
                foreach ($validated['details'] as $item) {
                    $assetRequest->details()
                        ->where('RequestDetailID', $item['RequestDetailID'])
                        ->update([
                            'PropertyClassification' => $item['PropertyClassification'],
                            'AssignedEndUserID' => $item['AssignedEndUserID'],
                            'AssignedDepartmentID' => $item['AssignedDepartmentID'],
                            'AdminNotes' => $item['AdminNotes'],
                        ]);
                }
            }

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
                    'items_reviewed' => isset($validated['details']) ? count($validated['details']) : 0,
                    'notes' => $validated['ReviewNotes'] ?? null
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset request approved successfully',
                'request' => $assetRequest->load(['details', 'reviewer'])
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
            'details' => 'nullable|array',
            'details.*.RequestDetailID' => 'required|integer',
            'details.*.PropertyClassification' => 'nullable|in:ICS,PAR',
            'details.*.AssignedEndUserID' => 'nullable|integer',
            'details.*.AssignedDepartmentID' => 'nullable|integer',
            'details.*.AdminNotes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check authorization (Admin or GSO Staff)
        if (!in_array($user->UserRoleID, [1, 2])) { // Admin or GSO Staff
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
            // Update review data for each item
            if (isset($validated['details'])) {
                foreach ($validated['details'] as $item) {
                    $assetRequest->details()
                        ->where('RequestDetailID', $item['RequestDetailID'])
                        ->update([
                            'PropertyClassification' => $item['PropertyClassification'],
                            'AssignedEndUserID' => $item['AssignedEndUserID'],
                            'AssignedDepartmentID' => $item['AssignedDepartmentID'],
                            'AdminNotes' => $item['AdminNotes'],
                        ]);
                }
            }

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
                    'items_reviewed' => isset($validated['details']) ? count($validated['details']) : 0,
                    'reason' => $validated['ReviewNotes']
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset request rejected',
                'request' => $assetRequest->load(['details', 'reviewer'])
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
     * Save review data for asset request items (Admin only)
     */
    public function saveReview(Request $request, $id)
    {
        $validated = $request->validate([
            'ReviewNotes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.RequestDetailID' => 'required|exists:assetrequestdetail,RequestDetailID',
            'items.*.PropertyClassification' => 'required|in:ICS,PAR',
            'items.*.AssignedEndUserID' => 'nullable|exists:enduser,EndUserID',
            'items.*.AssignedDepartmentID' => 'nullable|exists:department,DepartmentID',
            'items.*.AdminNotes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        if (!in_array($user->RoleID, [1, 4])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assetRequest = AssetRequest::with('details')->findOrFail($id);

        if (!$assetRequest->canBeReviewed()) {
            return response()->json([
                'message' => 'Request cannot be reviewed',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        // Validate that the items belong to this request
        $requestDetailIds = $assetRequest->details->pluck('RequestDetailID')->toArray();
        foreach ($validated['items'] as $item) {
            if (!in_array($item['RequestDetailID'], $requestDetailIds)) {
                return response()->json([
                    'message' => 'Invalid item: Does not belong to this request'
                ], 422);
            }
        }

        // Business logic validation
        foreach ($validated['items'] as $item) {
            if ($item['PropertyClassification'] === 'PAR' && empty($item['AssignedEndUserID'])) {
                return response()->json([
                    'message' => 'PAR items must be assigned to an individual'
                ], 422);
            }
            
            if ($item['PropertyClassification'] === 'ICS' && empty($item['AssignedDepartmentID'])) {
                return response()->json([
                    'message' => 'ICS items must be assigned to a department'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Update request with review notes
            $assetRequest->update([
                'ReviewNotes' => $validated['ReviewNotes'],
                'ReviewedBy' => $user->UserID,
                'ReviewedAt' => now(),
            ]);

            // Update each item with admin assignments
            foreach ($validated['items'] as $item) {
                $assetRequest->details()
                    ->where('RequestDetailID', $item['RequestDetailID'])
                    ->update([
                        'PropertyClassification' => $item['PropertyClassification'],
                        'AssignedEndUserID' => $item['AssignedEndUserID'],
                        'AssignedDepartmentID' => $item['AssignedDepartmentID'],
                        'AdminNotes' => $item['AdminNotes'],
                    ]);
            }

            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'reviewed',
                $user->UserID,
                [
                    'request_number' => $assetRequest->RequestNumber,
                    'items_reviewed' => count($validated['items']),
                    'notes' => $validated['ReviewNotes'] ?? null
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Review data saved successfully',
                'request' => $assetRequest->load(['details', 'reviewer'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save review data',
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
            'inventory:InventoryID,ItemDescription,Brand,ModelNumber,SerialNumber',
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

    /**
     * Get end users for assignment
     */
    public function getEndUsers()
    {
        $endUsers = \App\Models\EndUser::with(['department:DepartmentID,DepartmentName', 'position:PositionID,PositionName'])
            ->where('IsActive', true)
            ->orderBy('FullName')
            ->get();

        return response()->json($endUsers);
    }

    /**
     * Get departments for assignment
     */
    public function getDepartments()
    {
        $departments = \App\Models\Auth\Department::where('IsActive', true)
            ->orderBy('DepartmentName')
            ->get(['DepartmentID', 'DepartmentName', 'DepartmentCode']);

        return response()->json($departments);
    }
}
