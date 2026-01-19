<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\AssetAuditLog;
use App\Models\Inventory\PropertyInventoryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetIssuanceController extends Controller
{
    /**
     * Get all asset issuances
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AssetIssuance::with([
            'request.requester:UserID,FirstName,LastName',
            'request.department:DepartmentID,DepartmentName',
            'inventoryDetail.propertyInventory:InventoryID,ItemDescription,Brand,ModelNumber',
            'inventoryDetail.propertyType:PropertyTypeID,PropertyName',
            'issuedToUser:UserID,FirstName,LastName',
            'issuer:UserID,FirstName,LastName',
            'acknowledger:UserID,FirstName,LastName'
        ]);

        // Role-based filtering
        if ($user->RoleID === 3) { // Department Head
            $query->whereHas('request', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        } elseif ($user->RoleID === 2) { // Staff
            $query->where('IssuedTo', $user->UserID);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        // Document type filter
        if ($request->filled('document_type')) {
            $query->where('DocumentType', strtoupper($request->document_type));
        }

        $issuances = $query->orderBy('IssuanceDate', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($issuances);
    }

    /**
     * Get single asset issuance
     */
    public function show($id)
    {
        $issuance = AssetIssuance::with([
            'request.requester:UserID,FirstName,LastName,Email',
            'request.department:DepartmentID,DepartmentName,DepartmentCode',
            'inventoryDetail.propertyInventory:InventoryID,ItemDescription,Brand,ModelNumber,SerialNumber',
            'inventoryDetail.propertyType:PropertyTypeID,PropertyName,PropertyClassificationID',
            'inventoryDetail.propertyType.propertyClassification:PropertyClassificationID,ClassificationName,DocumentType',
            'inventoryDetail.condition:ConditionID,ConditionName',
            'issuedToUser:UserID,FirstName,LastName,Email',
            'issuer:UserID,FirstName,LastName',
            'acknowledger:UserID,FirstName,LastName',
            'auditLogs.performer:UserID,FirstName,LastName'
        ])->findOrFail($id);

        // Append signature URL if exists
        if ($issuance->AcknowledgmentSignature) {
            $issuance->signature_url = $issuance->getSignatureUrlAttribute();
        }

        return response()->json($issuance);
    }

    /**
     * Issue asset (Create PAR/ICS document)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'RequestID' => 'required|exists:asset_requests,RequestID',
            'IssuanceNotes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check authorization
        if (!in_array($user->RoleID, [1, 4])) { // Admin or Property Officer
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assetRequest = AssetRequest::with([
            'inventoryDetail.propertyType.propertyClassification'
        ])->findOrFail($validated['RequestID']);

        if (!$assetRequest->canBeIssued()) {
            return response()->json([
                'message' => 'Request is not approved yet or already issued',
                'current_status' => $assetRequest->Status
            ], 422);
        }

        // Check inventory availability again
        $inventoryDetail = PropertyInventoryDetail::findOrFail($assetRequest->PropertyInventoryDetailID);
        if ($inventoryDetail->QuantityAvailable < $assetRequest->QuantityRequested) {
            return response()->json([
                'message' => 'Insufficient quantity in inventory',
                'available' => $inventoryDetail->QuantityAvailable,
                'requested' => $assetRequest->QuantityRequested
            ], 422);
        }

        // Determine document type based on property classification
        $documentType = $assetRequest->inventoryDetail->propertyType->propertyClassification->DocumentType ?? 'PAR';

        DB::beginTransaction();
        try {
            // Create issuance record
            $issuance = AssetIssuance::create([
                'RequestID' => $assetRequest->RequestID,
                'DocumentType' => $documentType,
                'DocumentNumber' => AssetIssuance::generateDocumentNumber($documentType),
                'PropertyInventoryDetailID' => $assetRequest->PropertyInventoryDetailID,
                'QuantityIssued' => $assetRequest->QuantityRequested,
                'IssuedTo' => $assetRequest->RequestedBy,
                'IssuedBy' => $user->UserID,
                'IssuanceDate' => now(),
                'Status' => 'pending_acknowledgment',
                'IssuanceNotes' => $validated['IssuanceNotes'] ?? null,
            ]);

            // Deduct from inventory
            $inventoryDetail->decrement('QuantityAvailable', $assetRequest->QuantityRequested);

            // Update request status
            $assetRequest->update([
                'Status' => 'issued',
                'IssuedBy' => $user->UserID,
                'IssuedAt' => now(),
            ]);

            // Create audit logs
            AssetAuditLog::createLog(
                'asset_request',
                $assetRequest->RequestID,
                'issued',
                $user->UserID,
                [
                    'document_number' => $issuance->DocumentNumber,
                    'document_type' => $documentType,
                    'quantity' => $assetRequest->QuantityRequested
                ]
            );

            AssetAuditLog::createLog(
                'asset_issuance',
                $issuance->IssuanceID,
                'created',
                $user->UserID,
                [
                    'document_number' => $issuance->DocumentNumber,
                    'issued_to' => $assetRequest->requester->FirstName . ' ' . $assetRequest->requester->LastName
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset issued successfully',
                'issuance' => $issuance->load(['request', 'inventoryDetail', 'issuedToUser', 'issuer'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to issue asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Acknowledge receipt of asset
     */
    public function acknowledge(Request $request, $id)
    {
        $validated = $request->validate([
            'signature' => 'required|string', // Base64 encoded signature image
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $issuance = AssetIssuance::findOrFail($id);

        // Check if user is the recipient
        if ($issuance->IssuedTo !== $user->UserID) {
            return response()->json(['message' => 'Unauthorized - You are not the recipient'], 403);
        }

        if (!$issuance->canBeAcknowledged()) {
            return response()->json([
                'message' => 'Issuance cannot be acknowledged',
                'current_status' => $issuance->Status
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Save signature
            $signatureData = $validated['signature'];
            if (preg_match('/^data:image\/(\w+);base64,/', $signatureData, $type)) {
                $signatureData = substr($signatureData, strpos($signatureData, ',') + 1);
                $type = strtolower($type[1]);
                
                $signatureData = base64_decode($signatureData);
                if ($signatureData === false) {
                    throw new \Exception('Base64 decode failed');
                }

                $fileName = 'signatures/issuance_' . $issuance->IssuanceID . '_' . time() . '.png';
                Storage::disk('public')->put($fileName, $signatureData);

                $issuance->update([
                    'Status' => 'acknowledged',
                    'AcknowledgedBy' => $user->UserID,
                    'AcknowledgedAt' => now(),
                    'AcknowledgmentSignature' => $fileName,
                    'AcknowledgmentNotes' => $validated['notes'] ?? null,
                ]);

                // Create audit log
                AssetAuditLog::createLog(
                    'asset_issuance',
                    $issuance->IssuanceID,
                    'acknowledged',
                    $user->UserID,
                    [
                        'document_number' => $issuance->DocumentNumber,
                        'acknowledged_at' => now()->toDateTimeString()
                    ]
                );

                DB::commit();

                return response()->json([
                    'message' => 'Asset acknowledgment completed successfully',
                    'issuance' => $issuance->load(['acknowledger'])
                ]);
            }

            throw new \Exception('Invalid signature format');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to acknowledge asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get issuances for a specific request
     */
    public function getByRequest($requestId)
    {
        $issuances = AssetIssuance::with([
            'inventoryDetail.propertyInventory',
            'issuedToUser',
            'issuer',
            'acknowledger'
        ])
        ->where('RequestID', $requestId)
        ->get();

        return response()->json($issuances);
    }

    /**
     * Get approved asset requests ready for issuance
     */
    public function getApprovedRequests(Request $request)
    {
        $user = Auth::user();
        
        // Check authorization
        if (!in_array($user->RoleID, [1, 4])) { // Admin or Property Officer
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = AssetRequest::with([
            'requester:UserID,FirstName,LastName,FullName',
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'details.inventoryDetail.propertyInventory',
            'reviewer:UserID,FirstName,LastName'
        ])
        ->where('Status', 'approved')
        ->whereNull('IssuedBy'); // Not yet issued

        $requests = $query->orderBy('RequestDate', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json($requests);
    }

    /**
     * Generate PAR/ICS document (PDF)
     */
    public function generateDocument($id, $type)
    {
        $issuance = AssetIssuance::with([
            'request.requester:UserID,FirstName,LastName,FullName,Email',
            'request.department:DepartmentID,DepartmentName,DepartmentCode',
            'inventoryDetail.propertyInventory',
            'inventoryDetail.propertyType',
            'inventoryDetail.unitOfMeasure',
            'issuedToUser:UserID,FirstName,LastName,FullName,Email',
            'issuer:UserID,FirstName,LastName,FullName',
            'acknowledger:UserID,FirstName,LastName,FullName'
        ])->findOrFail($id);

        // Validate document type
        if (!in_array($type, ['PAR', 'ICS']) || $type !== $issuance->DocumentType) {
            return response()->json(['message' => 'Invalid document type'], 400);
        }

        // For now, return a simple JSON response
        // In production, you would generate an actual PDF here
        return response()->json([
            'document_type' => $type,
            'document_number' => $issuance->DocumentNumber,
            'issuance' => $issuance,
            'message' => 'Document generation would happen here - PDF generation not implemented in this demo'
        ]);
    }

    /**
     * Return an issued asset
     */
    public function returnAsset(Request $request, $id)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $issuance = AssetIssuance::findOrFail($id);

        // Check authorization (Admin or Property Officer or the recipient)
        if (!in_array($user->RoleID, [1, 4]) && $issuance->IssuedTo !== $user->UserID) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($issuance->Status !== 'acknowledged') {
            return response()->json([
                'message' => 'Only acknowledged assets can be returned',
                'current_status' => $issuance->Status
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update issuance status
            $issuance->update([
                'Status' => 'returned',
                'AcknowledgmentNotes' => $issuance->AcknowledgmentNotes . '\n\nReturned: ' . ($validated['notes'] ?? 'No notes provided'),
            ]);

            // Return to inventory
            $inventoryDetail = PropertyInventoryDetail::findOrFail($issuance->PropertyInventoryDetailID);
            $inventoryDetail->increment('QuantityAvailable', $issuance->QuantityIssued);

            // Create audit log
            AssetAuditLog::createLog(
                'asset_issuance',
                $issuance->IssuanceID,
                'returned',
                $user->UserID,
                [
                    'document_number' => $issuance->DocumentNumber,
                    'returned_by' => $user->FirstName . ' ' . $user->LastName,
                    'notes' => $validated['notes'] ?? null
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset returned successfully',
                'issuance' => $issuance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to return asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dispose of an asset
     */
    public function disposeAsset(Request $request, $id)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Check authorization (Admin or Property Officer only)
        if (!in_array($user->RoleID, [1, 4])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $issuance = AssetIssuance::findOrFail($id);

        if (!in_array($issuance->Status, ['acknowledged', 'returned'])) {
            return response()->json([
                'message' => 'Only acknowledged or returned assets can be disposed',
                'current_status' => $issuance->Status
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update issuance status
            $issuance->update([
                'Status' => 'disposed',
                'AcknowledgmentNotes' => $issuance->AcknowledgmentNotes . '\n\nDisposed: ' . $validated['notes'],
            ]);

            // If it was returned, we need to remove it from inventory again
                if ($issuance->Status === 'returned') {
                $inventoryDetail = PropertyInventoryDetail::findOrFail($issuance->PropertyInventoryDetailID);
                $inventoryDetail->decrement('QuantityAvailable', $issuance->QuantityIssued);
            }

            // Create audit log
            AssetAuditLog::createLog(
                'asset_issuance',
                $issuance->IssuanceID,
                'disposed',
                $user->UserID,
                [
                    'document_number' => $issuance->DocumentNumber,
                    'disposed_by' => $user->FirstName . ' ' . $user->LastName,
                    'disposal_reason' => $validated['notes']
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Asset disposed successfully',
                'issuance' => $issuance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to dispose asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get issuance statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        
        // Check authorization
        if (!in_array($user->RoleID, [1, 4])) { // Admin or Property Officer
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_issuances' => AssetIssuance::count(),
            'pending_acknowledgment' => AssetIssuance::where('Status', 'pending_acknowledgment')->count(),
            'acknowledged' => AssetIssuance::where('Status', 'acknowledged')->count(),
            'returned' => AssetIssuance::where('Status', 'returned')->count(),
            'disposed' => AssetIssuance::where('Status', 'disposed')->count(),
            'par_count' => AssetIssuance::where('DocumentType', 'PAR')->count(),
            'ics_count' => AssetIssuance::where('DocumentType', 'ICS')->count(),
        ];

        return response()->json($stats);
    }
}
