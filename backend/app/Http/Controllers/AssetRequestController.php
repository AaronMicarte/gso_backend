<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetAuditLog;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Services\AssetRequestSignatureService;
use App\Models\AssetAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MasterData\DepartmentResource;

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
        $roleName = $user->userRole->RoleName ?? '';
        if (in_array($roleName, ['Admin', 'GSO Staff'])) {
            // Can see all
        } else {
            // All other roles can only see requests from their department
            $query->where('DepartmentID', $user->DepartmentID);
        }

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
            'details.inventoryDetail.propertyType:PropertyTypeID,PropertyName',
            'details.inventoryDetail.propertyInventory:InventoryID,ItemDescription,Brand,ModelNumber,SerialNumber',
            'details.inventoryDetail.condition:ConditionID,ConditionName',
            'reviewer:UserID,FullName',
            'issuer:UserID,FullName',
            'auditLogs.performer',
            'signatures.signedByUser:UserID,FullName',
            'details.assignments',

            // Real GSO issuance data (propertyissuance + propertyissuancedetail)
            'propertyIssuance' => function ($q) {
                $q->with([
                    'issuedBy:UserID,FullName',
                    'issuedTo:EndUserID,FullName,DepartmentID',
                    'details.inventoryDetail' => function ($iq) {
                        $iq->withTrashed();
                    },
                    'details.inventoryDetail.inventory' => function ($iq) {
                        $iq->withTrashed();
                    },
                    'details.inventoryDetail.unitOfMeasure',
                ])->orderBy('DateIssued', 'desc');
            },
        ])->findOrFail($id);

        // Role-based access control for viewing
        $user = Auth::user();
        $roleName = $user->userRole->RoleName ?? '';
        $canView = false;

        if (in_array($roleName, ['Admin', 'GSO Staff'])) {
            $canView = true;
        } else {
            $canView = $request->DepartmentID === $user->DepartmentID;
        }

        if (!$canView) {
            return response()->json(['message' => 'Unauthorized to view this asset request'], 403);
        }

        // Map propertyIssuance to the frontend's expected `issuances` array
        $requestArr = $request->toArray();
        $propertyIssuances = $request->propertyIssuance ?? collect();
        $requestArr['issuances'] = $propertyIssuances
            ->map(function ($issuance) use ($request) {
                $issuedToUser = null;

                // For ICS, recipient should be the request's requester (dept head)
                if ($issuance->DocumentType === 'ICS' && $request->requester) {
                    $issuedToUser = $request->requester;
                } elseif ($issuance->issuedTo) {
                    $issuedToUser = $issuance->issuedTo;
                } else {
                    $issuedToUser = [
                        'FullName' => $issuance->IssuedToName ?? 'External Recipient',
                        'department' => null,
                    ];
                }

                return [
                    'IssuanceID' => $issuance->IssuanceID,
                    'RequestID' => $issuance->RequestID,
                    'DocumentType' => $issuance->DocumentType,
                    'DocumentNumber' => $issuance->DocumentNumber,
                    'IssuedTo' => $issuance->IssuedTo,
                    'IssuedToName' => $issuance->IssuedToName,
                    'IssuedBy' => $issuance->IssuedBy,
                    'IssuanceDate' => $issuance->DateIssued,
                    'Status' => $issuance->Status,

                    // IMPORTANT: request page expects IssuanceNotes
                    'IssuanceNotes' => $issuance->Remarks,
                    // Keep Remarks too for compatibility with other pages
                    'Remarks' => $issuance->Remarks,

                    'AcknowledgedBy' => $issuance->AcknowledgedBy ?? null,
                    'AcknowledgedAt' => $issuance->AcknowledgedAt,
                    'AcknowledgmentSignature' => $issuance->AcknowledgmentSignature,
                    'AcknowledgmentNotes' => $issuance->AcknowledgmentNotes,

                    'created_at' => $issuance->CreatedDate ?? null,
                    'updated_at' => $issuance->UpdatedDate ?? null,
                    'deleted_at' => $issuance->DeletedDate ?? null,

                    'issuer' => $issuance->issuedBy,
                    'issuedToUser' => $issuedToUser,
                    'details' => $issuance->details ? $issuance->details->map(function ($detail) {
                        return [
                            'IssuanceDetailID' => $detail->IssuanceDetailID,
                            'IssuanceID' => $detail->IssuanceID,
                            'RequestDetailID' => $detail->RequestDetailID,
                            'InventoryDetailID' => $detail->InventoryDetailID,
                            'QuantityIssued' => $detail->QuantityIssued,
                            'ItemDescription' => $detail->ItemDescription,
                            'Remarks' => $detail->Remarks,
                            'created_at' => $detail->created_at,
                            'updated_at' => $detail->updated_at,
                            'inventoryDetail' => $detail->inventoryDetail,
                        ];
                    })->values()->all() : [],
                ];
            })
            ->values()
            ->all();

        unset($requestArr['property_issuance']);

        return response()->json($requestArr);
    }

    /**
     * Create new asset request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Allow Admin (1), GSO Staff (2), Property Custodian (3), and Department Head (4)
        $allowedRoles = [1, 2, 3, 4];
        
        if (!in_array($user->RoleID, $allowedRoles)) { 
            return response()->json([
                'message' => 'Unauthorized: You do not have permission to create asset requests. Your Role ID: ' . $user->RoleID
            ], 403);
        }

        $validated = $request->validate([
            'Purpose' => 'required|string|min:5|max:1000',
            'items' => 'required|array|min:1',
            'items.*.PropertyInventoryDetailID' => 'nullable|exists:propertyinventorydetail,InventoryDetailID',
            'items.*.ItemDescription' => 'required|string|max:255',
            'items.*.Unit' => 'nullable|string|max:50',
            'items.*.QuantityRequested' => 'required|integer|min:1',
            'items.*.Purpose' => 'required|string|max:1000',
            'items.*.IntendedEndUser' => 'nullable|string|max:255',
            'items.*.AdditionalNotes' => 'nullable|string|max:1000',
            'requesterSignature' => 'required|string', // base64 data URL
            'requesterSignatureSource' => 'required|in:canvas,upload',
        ]);

        $user = Auth::user();

        // Validate inventory availability ONLY for inventory items
        foreach ($validated['items'] as $item) {
            if (!empty($item['PropertyInventoryDetailID'])) {
                $inventoryDetail = PropertyInventoryDetail::with('propertyInventory')->findOrFail($item['PropertyInventoryDetailID']);
                if ($inventoryDetail->QuantityAvailable < $item['QuantityRequested']) {
                    return response()->json([
                        'message' => 'Insufficient quantity available for item: ' . ($inventoryDetail->propertyInventory->ItemDescription ?? 'Unknown Item'),
                        'available' => $inventoryDetail->QuantityAvailable,
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

            // Process requester signature
            $signatureService = new AssetRequestSignatureService();
            $signatureService->processAndStore(
                $assetRequest->RequestID,
                'requester',
                $validated['requesterSignature'],
                $validated['requesterSignatureSource'],
                $user->UserID
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
            // Allow PropertyClassification to be omitted for items that are NOT approved for issuance.
            'details.*.PropertyClassification' => 'nullable|in:ICS,PAR',
            'details.*.AssignedEndUserID' => 'nullable|exists:enduser,EndUserID',
            'details.*.AssignedDepartmentID' => 'nullable|integer',
            'details.*.AdminNotes' => 'nullable|string|max:500',
            'details.*.IsApprovedForIssuance' => 'required|boolean',
            'approverSignature' => 'required|string', // base64 data URL
            'approverSignatureSource' => 'required|in:canvas,upload',
        ]);

        $user = Auth::user();

        // Check authorization (Admin or Property Officer)
        if (!in_array($user->UserRoleID, [1, 4])) { // Admin or Property Officer
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assetRequest = AssetRequest::findOrFail($id);

        if (!$assetRequest->canBeApproved()) {
            return response()->json([
                'message' => 'Request cannot be approved',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        // Conditional validation: only require PropertyClassification for items marked
        // as IsApprovedForIssuance. This prevents forcing classification on unapproved lines.
        foreach (($validated['details'] ?? []) as $idx => $item) {
            if (!empty($item['IsApprovedForIssuance'])) {
                $pc = $item['PropertyClassification'] ?? null;
                if (empty($pc) || !in_array($pc, ['ICS', 'PAR'], true)) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => ["details.$idx.PropertyClassification" => ['PropertyClassification is required for items approved for issuance']]
                    ], 422);
                }
            }
        }

        $approvedDetails = array_filter($validated['details'] ?? [], function ($item) {
            return !empty($item['IsApprovedForIssuance']);
        });

        if (empty($approvedDetails)) {
            return response()->json([
                'message' => 'At least one item must be approved for issuance.',
                'error' => 'no_items_approved'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update review data for each item
            if (isset($validated['details'])) {
                foreach ($validated['details'] as $item) {
                    $classification = $item['PropertyClassification'] ?? null;
                    $assignedDepartmentId = $item['AssignedDepartmentID'] ?? null;

                    // Ensure department isn't left null for accountability grouping.
                    if (empty($assignedDepartmentId) && in_array($classification, ['ICS', 'PAR'], true)) {
                        $assignedDepartmentId = $assetRequest->DepartmentID;
                    }

                    $assetRequest->details()
                        ->where('RequestDetailID', $item['RequestDetailID'])
                        ->update([
                            'PropertyClassification' => $item['PropertyClassification'],
                            'AssignedEndUserID' => $item['AssignedEndUserID'],
                            'AssignedDepartmentID' => $assignedDepartmentId,
                            'AdminNotes' => $item['AdminNotes'],
                            'IsApprovedForIssuance' => (bool) $item['IsApprovedForIssuance'],
                        ]);
                }
            }

            $assetRequest->update([
                // Use a distinct status to indicate the request is approved and awaiting issuance
                'Status' => 'waiting_for_issuance',
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

            // Create issuance assignments for each item
            if (isset($validated['details'])) {
                foreach ($validated['details'] as $item) {
                    if (empty($item['IsApprovedForIssuance'])) {
                        continue;
                    }

                    $classification = $item['PropertyClassification'] ?? null;
                    $assignedDepartmentId = $item['AssignedDepartmentID'] ?? null;

                    if (empty($assignedDepartmentId) && in_array($classification, ['ICS', 'PAR'], true)) {
                        $assignedDepartmentId = $assetRequest->DepartmentID;
                    }

                    AssetAssignment::create([
                        'AssetRequestDetailID' => $item['RequestDetailID'],
                        'AssignedUserID' => $item['AssignedEndUserID'],
                        'AssignedDepartmentID' => $assignedDepartmentId,
                        'AssignmentType' => 'issuance',
                        'PropertyClassification' => $item['PropertyClassification'],
                        'AdminNotes' => $item['AdminNotes'],
                        'AssignedBy' => $user->UserID,
                        'AssignedAt' => now(),
                    ]);
                }
            }

            // Save approver signature
            if (isset($validated['approverSignature']) && isset($validated['approverSignatureSource'])) {
                $signatureService = new AssetRequestSignatureService();
                $signatureService->processAndStore(
                    $assetRequest->RequestID,
                    'approver',
                    $validated['approverSignature'],
                    $validated['approverSignatureSource'],
                    $user->UserID
                );
            }

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
            'details.*.AssignedEndUserID' => 'nullable|exists:enduser,EndUserID',
            'details.*.AssignedDepartmentID' => 'nullable|integer',
            'details.*.AdminNotes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check authorization (Admin or Property Officer)
        if (!in_array($user->UserRoleID, [1, 4])) { // Admin or Property Officer
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
     * Confirm receipt of issued items (Assigned end user or department member)
     */
    public function receive(Request $request, $id)
    {
        $user = Auth::user();
        $assetRequest = AssetRequest::with('details.assignments')->findOrFail($id);

        if ($assetRequest->Status !== 'issued' && $assetRequest->Status !== 'approved') {
            return response()->json([
                'message' => 'Request must be issued before receipt confirmation',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        // Check if user is authorized to confirm receipt
        $isAuthorized = false;
        foreach ($assetRequest->details as $detail) {
            // Get the latest issuance assignment
            $latestIssuance = $detail->assignments
                ->where('AssignmentType', 'issuance')
                ->sortByDesc('AssignedAt')
                ->first();

            if ($latestIssuance) {
                if ($latestIssuance->AssignedUserID === $user->UserID) {
                    $isAuthorized = true;
                    break;
                }
                if ($latestIssuance->AssignedDepartmentID === $user->DepartmentID) {
                    $isAuthorized = true;
                    break;
                }
            }
        }

        if (!$isAuthorized) {
            return response()->json(['message' => 'Unauthorized: You are not assigned to receive these items'], 403);
        }

        DB::beginTransaction();
        try {
            $assetRequest->update([
                'Status' => 'received',
                'IssuedAt' => now(), // Update issued timestamp if not set
            ]);

            // Create audit log
            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'received',
                $user->UserID,
                [
                    'request_number' => $assetRequest->RequestNumber,
                    'received_by' => $user->FullName,
                    'department' => $user->department->DepartmentName ?? 'Unknown'
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Receipt confirmed successfully',
                'request' => $assetRequest->load(['details.assignments'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to confirm receipt',
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
            'details' => 'required|array|min:1',
            'details.*.RequestDetailID' => 'required|exists:assetrequestdetail,RequestDetailID',
            // PropertyClassification may be omitted for items that are NOT approved for issuance.
            'details.*.PropertyClassification' => 'nullable|in:ICS,PAR',
            'details.*.AssignedEndUserID' => 'nullable|exists:enduser,EndUserID',
            'details.*.AssignedDepartmentID' => 'nullable|exists:department,DepartmentID',
            'details.*.AdminNotes' => 'nullable|string|max:1000',
            'details.*.IsApprovedForIssuance' => 'required|boolean',
        ]);

        $user = Auth::user();

        if (!in_array($user->UserRoleID, [1, 4])) {
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
        foreach ($validated['details'] as $item) {
            if (!in_array($item['RequestDetailID'], $requestDetailIds)) {
                return response()->json([
                    'message' => 'Invalid item: Does not belong to this request'
                ], 422);
            }
        }

        // Business logic validation
        $detailsById = $assetRequest->details->keyBy('RequestDetailID');
        foreach ($validated['details'] as $item) {
            // Only enforce classification-specific business rules for items that were
            // approved for issuance. Unapproved lines may omit classification.
            if (empty($item['IsApprovedForIssuance'])) {
                continue;
            }

            $classification = $item['PropertyClassification'] ?? null;
            $requestDetail = $detailsById->get($item['RequestDetailID']);

            if ($classification === 'PAR') {
                // PAR uses free-text recipient name (IntendedEndUser) and will resolve/create EndUser at issuance time.
                // We still require the name to be present for real-world document integrity.
                if (!$requestDetail || empty(trim((string) $requestDetail->IntendedEndUser))) {
                    return response()->json([
                        'message' => 'PAR items must have an individual name (IntendedEndUser)'
                    ], 422);
                }
            }

            if ($classification === 'ICS' && empty($item['AssignedEndUserID'])) {
                return response()->json([
                    'message' => 'ICS items must be assigned to an End User'
                ], 422);
            }
            
            if ($classification === 'ICS' && empty($item['AssignedDepartmentID'])) {
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

            // Create asset assignments for each item
            foreach ($validated['details'] as $item) {
                $classification = $item['PropertyClassification'] ?? null;
                $assignedDepartmentId = $item['AssignedDepartmentID'] ?? null;

                if (empty($assignedDepartmentId) && in_array($classification, ['ICS', 'PAR'], true)) {
                    $assignedDepartmentId = $assetRequest->DepartmentID;
                }

                AssetAssignment::create([
                    'AssetRequestDetailID' => $item['RequestDetailID'],
                    'AssignedUserID' => $item['AssignedEndUserID'],
                    'AssignedDepartmentID' => $assignedDepartmentId,
                    'AssignmentType' => 'request',
                    'PropertyClassification' => $item['PropertyClassification'],
                    'AdminNotes' => $item['AdminNotes'],
                    'AssignedBy' => $user->UserID,
                    'AssignedAt' => now(),
                ]);
            }

            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'reviewed',
                $user->UserID,
                [
                    'request_number' => $assetRequest->RequestNumber,
                    'items_reviewed' => count($validated['details']),
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
        try {
            $query = PropertyInventoryDetail::leftJoin('propertytype', 'propertyinventorydetail.PropertyTypeID', '=', 'propertytype.PropertyTypeID')
                ->leftJoin('propertyinventory', 'propertyinventorydetail.InventoryID', '=', 'propertyinventory.InventoryID')
                ->leftJoin('propertystatus', 'propertyinventorydetail.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                ->leftJoin('condition', 'propertyinventorydetail.ConditionID', '=', 'condition.ConditionID')
                ->leftJoin('unitofmeasure', 'propertyinventorydetail.UnitOfMeasureID', '=', 'unitofmeasure.UnitOfMeasureID')
                ->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
                // Exclude inventory which is already assigned for issuance or reserved by a request that is approved/waiting for issuance
                ->leftJoin('asset_assignments', 'propertyinventorydetail.InventoryDetailID', '=', 'asset_assignments.PropertyInventoryDetailID')
                ->leftJoin('assetrequestdetail', 'propertyinventorydetail.InventoryDetailID', '=', 'assetrequestdetail.PropertyInventoryDetailID')
                ->leftJoin('assetrequest', 'assetrequestdetail.RequestID', '=', 'assetrequest.RequestID')
                ->select(
                    'propertyinventorydetail.*',
                    'propertytype.PropertyName',
                    'propertyinventory.InventoryNumber',
                    'propertyinventory.DateReceived',
                    'propertystatus.StatusName',
                    'condition.ConditionName',
                    'unitofmeasure.UnitName',
                    'deliveryreceiptdetail.ItemDescription as dr_item_description'
                )
                ->where('propertyinventorydetail.QuantityAvailable', '>', 0)
                ->where(function($q) {
                    $q->whereNull('propertyinventorydetail.ConditionID')->orWhere('propertyinventorydetail.ConditionID', '!=', 4);
                })
                ->where(function($q) {
                    // Exclude details already assigned for issuance
                    $q->whereNull('asset_assignments.AssignmentID')->orWhere('asset_assignments.AssignmentType', '<>', 'issuance');
                })
                ->where(function($q) {
                    // Exclude inventory tied to requests that are approved or waiting for issuance
                    $q->whereNull('assetrequest.RequestID')->orWhereNotIn('assetrequest.Status', ['approved', 'waiting_for_issuance']);
                });

            $items = $query->orderBy('propertyinventorydetail.PropertyNumber', 'asc')->get();

            return response()->json($items);
        } catch (\Throwable $e) {
            // Log for server-side debugging and return a clearer JSON error for dev/testing
            \Log::error('getAvailableInventory failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error retrieving available inventory. Check server logs for details.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $departments = \App\Models\Auth\Department::query()
            ->where('IsActive', true)
            ->orderBy('DepartmentName')
            ->get(['DepartmentID', 'DepartmentCode', 'DepartmentName']);

        // Keep this endpoint stable for the asset issuance workflow.
        // Use the column names the frontend already consumes.
        return response()->json($departments);
    }
}
