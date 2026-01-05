<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetIssuance;
use App\Models\AssetAuditLog;
use App\Models\PropertyInventoryDetail;
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
            'inventoryDetail.propertyType:PropertyTypeID,PropertyTypeName',
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
            'inventoryDetail.propertyType:PropertyTypeID,PropertyTypeName,PropertyClassificationID',
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
        if ($inventoryDetail->QuantityOnHand < $assetRequest->QuantityRequested) {
            return response()->json([
                'message' => 'Insufficient quantity in inventory',
                'available' => $inventoryDetail->QuantityOnHand,
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
            $inventoryDetail->decrement('QuantityOnHand', $assetRequest->QuantityRequested);

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
}
