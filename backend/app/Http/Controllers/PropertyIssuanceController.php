<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetRequestDetail;
use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\AssetAuditLog;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\AssignedProperty;
use App\Models\QrCode;
use App\Models\EndUser;
use App\Models\RequisitionIssueSlip;
use App\Models\RequisitionIssueSlipItem;
use App\Models\RequisitionIssueSlipSignature;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\MasterData\PropertyStatus;
use App\Services\AssetRequestSignatureService;
use App\Services\Notifications\RoleNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class PropertyIssuanceController extends Controller
{
    private const ROLE_ADMIN = 1;
    private const ROLE_GSO_STAFF = 2;
    /**
     * Get all property issuances
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PropertyIssuance::with([
            'request.requester.department:DepartmentID,DepartmentName',
            'request.department:DepartmentID,DepartmentName',
            'request.ris.items',
            'deliveryReceipt.requestedByUser:UserID,FullName,DepartmentID',
            'deliveryReceipt.requestedByUser.department:DepartmentID,DepartmentName',
            'deliveryReceipt.supplier', // ADD THIS
            'issuedBy',
            'issuedTo',
            'details.inventoryDetail:InventoryDetailID,Description,Brand,ModelNumber,PropertyNumber,SerialNumber,UnitCost',
            'details.inventoryDetail.inventory',
            'details.inventoryDetail.unitOfMeasure',
        ]);

        // Role-based filtering
        $roleName = $user->userRole->RoleName ?? '';
        if (in_array($roleName, ['Admin', 'GSO Staff'])) {
            // Can see all
        } else {
            // All other roles can only see issuances from their department
            $query->whereHas('request', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        }

        // Status filter (based on request status)
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                $query->whereNotNull('DateIssued');
            }
        }

        $issuances = $query->orderBy('DateIssued', 'desc')
            ->orderBy('IssuanceID', 'desc')
            ->paginate($request->get('per_page', 15));

        // Transform data to group by request
        $requestGroups = $issuances->getCollection()->groupBy(function ($issuance) {
            if (!empty($issuance->RequestID)) {
                return 'REQ_' . $issuance->RequestID;
            }

            return 'DR_' . ($issuance->DRID ?? 'UNKNOWN');
        });
        $transformedData = [];

        foreach ($requestGroups as $groupKey => $requestIssuances) {
            $firstIssuance = $requestIssuances->first();
            $request = $firstIssuance->request;
            $dr = $firstIssuance->deliveryReceipt;

            $groupId = $request ? (int) $request->RequestID : (int) ($dr->DRID ?? 0);
            $groupNumber = $request ? ($request->RequestNumber ?? 'N/A') : ($dr->DRNumber ?? 'DR');
            $groupDate = $request ? ($request->RequestDate ?? null) : ($dr->DateReceived ?? null);

            $requesterName = $request
                ? ($request->requester->FullName ?? 'Unknown User')
                : ($dr->requestedByUser->FullName ?? 'Unknown Requester');

            $departmentName = $request
                ? ($request->department->DepartmentName ?? ($request->requester->department->DepartmentName ?? 'Unknown Department'))
                : ($dr->requestedByUser->department->DepartmentName ?? 'Unknown Department');

            // Transform each issuance in the group
            $transformedIssuances = $requestIssuances->map(function ($issuance) {
                // Recipient handling
                // For ICS, the recipient should be the request's requester (dept head).
                if ($issuance->DocumentType === 'ICS' && $issuance->request && $issuance->request->requester) {
                    $issuance->issuedToUser = $issuance->request->requester;
                    $issuance->IssuedToName = $issuance->request->requester->FullName ?? $issuance->IssuedToName;
                } elseif ($issuance->issuedTo) {
                    $issuance->issuedToUser = $issuance->issuedTo;
                } else {
                    $issuance->issuedToUser = [
                        'FullName' => $issuance->IssuedToName ?? 'External Recipient',
                        'department' => null
                    ];
                }

                $issuance->issuer = $issuance->issuedBy;

                // Get all details for this issuance (normalize fields for frontend)
                $issuance->details = $issuance->details->map(function ($detail) {
                    if ($detail->inventoryDetail) {
                        $detail->inventoryDetail->propertyInventory = $detail->inventoryDetail->inventory;

                        // Prioritize the Issuance Detail's ItemDescription as the "Item Name" (e.g. "Laptop")
                        // and keep the Inventory Detail's Description as the "Description" (e.g. specs)
                        if ($detail->inventoryDetail->propertyInventory) {
                            // Frontend expects 'ItemDescription' to hold the name (e.g. Laptop)
                            // and we can append description if needed, or just use the name
                            $detail->inventoryDetail->propertyInventory->ItemDescription = $detail->ItemDescription ?? $detail->inventoryDetail->dr_item_description ?? $detail->inventoryDetail->Description ?? 'Item';
                            
                            $detail->inventoryDetail->propertyInventory->Brand = $detail->inventoryDetail->Brand ?? null;
                            $detail->inventoryDetail->propertyInventory->ModelNumber = $detail->inventoryDetail->ModelNumber ?? null;
                            $detail->inventoryDetail->propertyInventory->SerialNumber = $detail->inventoryDetail->SerialNumber ?? null;
                        } else {
                            // Fallback: create a minimal propertyInventory object using IssuanceDetail fields
                            $detail->inventoryDetail->propertyInventory = (object) [
                                'ItemDescription' => $detail->ItemDescription ?? $detail->inventoryDetail->Description ?? 'Item',
                                'Brand' => $detail->inventoryDetail->Brand ?? null,
                                'ModelNumber' => $detail->inventoryDetail->ModelNumber ?? null,
                                'SerialNumber' => $detail->inventoryDetail->SerialNumber ?? null,
                            ];
                        }

                        if (empty($detail->inventoryDetail->unitOfMeasure) && !empty($detail->Unit)) {
                            $detail->inventoryDetail->unitOfMeasure = (object) ['UnitName' => $detail->Unit];
                        }
                        
                        // Fix for UnitName access: if unitOfMeasure exists but UnitName is null or not set, try to map from direct unit
                         if (!empty($detail->inventoryDetail->unitOfMeasure) && empty($detail->inventoryDetail->unitOfMeasure->UnitName) && !empty($detail->Unit)) {
                            $detail->inventoryDetail->unitOfMeasure->UnitName = $detail->Unit;
                        }

                        // Ensure Description is set on the inventoryDetail itself if missing (backup)
                        if (empty($detail->inventoryDetail->Description) && !empty($detail->ItemDescription)) {
                             $detail->inventoryDetail->Description = $detail->ItemDescription;
                        }

                        unset($detail->inventoryDetail->inventory);
                    } else {
                        // No linked inventory detail - use issuance detail fields
                        $detail->inventoryDetail = (object) [
                            'InventoryDetailID' => null,
                            'InventoryID' => null,
                            'PropertyNumber' => $detail->PropertyNumber ?? null,
                            'UnitCost' => $detail->UnitCost ?? null,
                            'unitOfMeasure' => (object) ['UnitName' => $detail->Unit ?? null],
                            'propertyInventory' => (object) [
                                'ItemName' => $detail->ItemDescription ?? 'Item',
                                'ItemDescription' => null, // No long description available
                                'Brand' => null,
                                'ModelNumber' => null,
                                'SerialNumber' => null,
                            ],
                        ];
                    }

                    return $detail;
                });

                // Map field names
                $issuance->IssuanceDate = $issuance->DateIssued;
                $issuance->QuantityIssued = $issuance->details->sum('QuantityIssued');
                $issuance->IssuanceDate = $issuance->DateIssued;
                $issuance->QuantityIssued = $issuance->details->sum('QuantityIssued');
                $issuance->deliveryReceipt = $issuance->deliveryReceipt?->DRNumber
                    ? ['DRNumber' => $issuance->deliveryReceipt->DRNumber]
                    : null;
                unset($issuance->issuedTo, $issuance->issuedBy, $issuance->DateIssued);
                return $issuance;
            });

            $transformedData[] = [
                'RequestID' => $groupId,
                'RequestNumber' => $groupNumber,
                'RequestDate' => $groupDate,
                'requester' => [
                    'FullName' => $requesterName,
                    'Department' => $departmentName,
                ],
                'department' => [
                    'DepartmentName' => $departmentName,
                ],
                'issuances' => $transformedIssuances->values(),
                'totalItems' => $transformedIssuances->sum(function ($issuance) {
                    return $issuance->details->sum('QuantityIssued');
                })
            ];
        }

        $issuances->setCollection(collect($transformedData));

        return response()->json($issuances);
    }

    /**
     * Get single property issuance
     */
    public function show($id)
    {
        $issuance = PropertyIssuance::with([
            'request.requester.department:DepartmentID,DepartmentName',
            'request.department:DepartmentID,DepartmentName,DepartmentCode',
            'request.signatures',
            'request.ris.items',
            'deliveryReceipt.requestedByUser:UserID,FullName,DepartmentID',
            'deliveryReceipt.requestedByUser.department:DepartmentID,DepartmentName',
            'deliveryReceipt.supplier', // ADD THIS
            'issuedBy',
            'issuedBy.department:DepartmentID,DepartmentName',
            'issuedTo',
            'issuedTo.department:DepartmentID,DepartmentName',
            'details.inventoryDetail' => function ($query) {
                $query->withTrashed();
            },
            'details.inventoryDetail.propertyType' => function ($query) {
                $query->withTrashed();
            },
            'details.inventoryDetail.propertyType.propertyClassification' => function ($query) {
                $query->withTrashed();
            },
            'details.inventoryDetail.inventory' => function ($query) {
                $query->withTrashed();
            },
            'details.inventoryDetail.drDetail' => function ($query) {
                $query->withTrashed();
            },
            'details.inventoryDetail.unitOfMeasure',
            'details.inventoryDetail.qrCode',
        ])->findOrFail($id);

        // Role-based access control for viewing
        $user = Auth::user();
        $roleName = $user->userRole->RoleName ?? '';
        $canView = false;

        if (in_array($roleName, ['Admin', 'GSO Staff'])) {
            $canView = true;
        } else {
            $canView = $issuance->request && $issuance->request->DepartmentID === $user->DepartmentID;
        }

        if (!$canView) {
            return response()->json(['message' => 'Unauthorized to view this property issuance'], 403);
        }

        // Transform the issuance data to match frontend expectations
        $transformedIssuance = [
            'IssuanceID' => $issuance->IssuanceID,
            'RequestID' => $issuance->RequestID,
            'DRID' => $issuance->DRID,
            'DocumentType' => $issuance->DocumentType,
            'DocumentNumber' => $issuance->DocumentNumber,
            'IssuedTo' => $issuance->IssuedTo,
            'IssuedToName' => $issuance->IssuedToName,
            'IssuedBy' => $issuance->IssuedBy,
            'IssuanceDate' => $issuance->DateIssued,
            'Status' => $issuance->Status,
            'Remarks' => $issuance->Remarks,
            'IssuerSignature' => $issuance->IssuerSignature,
            'IssuerSignatureSource' => $issuance->IssuerSignatureSource,
            'AcknowledgedBy' => $issuance->AcknowledgedBy,
            'AcknowledgedAt' => $issuance->AcknowledgedAt,
            'AcknowledgmentSignature' => $issuance->AcknowledgmentSignature,
            'AcknowledgmentNotes' => $issuance->AcknowledgmentNotes,
            'created_at' => $issuance->CreatedDate,
            'updated_at' => $issuance->UpdatedDate,
            'deleted_at' => $issuance->DeletedDate,
        ];

        // Add relationships
        $transformedIssuance['request'] = $issuance->request;
      $transformedIssuance['deliveryReceipt'] = $issuance->deliveryReceipt ? [
            'DRID' => $issuance->deliveryReceipt->DRID,
            'DRNumber' => $issuance->deliveryReceipt->DRNumber,
            'RequestedBy' => $issuance->deliveryReceipt->requestedByUser->FullName ?? null,
            'DepartmentID' => $issuance->deliveryReceipt->requestedByUser->DepartmentID ?? null,
            'DepartmentName' => $issuance->deliveryReceipt->requestedByUser->department->DepartmentName ?? null,
            'DateReceived' => $issuance->deliveryReceipt->DateReceived,
            'SupplierName' => $issuance->deliveryReceipt->supplier->SupplierName ?? null, // ADD
            'PONumber' => $issuance->deliveryReceipt->PONumber ?? null,                   // ADD
        ] : null;
        $transformedIssuance['issuer'] = $issuance->issuedBy;

        // Handle issuedTo relationship
        // For ICS, the recipient should be the request's requester (dept head).
        if ($issuance->DocumentType === 'ICS' && $issuance->request && $issuance->request->requester) {
            $transformedIssuance['issuedToUser'] = $issuance->request->requester;
            $transformedIssuance['IssuedToName'] = $issuance->request->requester->FullName ?? $transformedIssuance['IssuedToName'];
        } elseif ($issuance->issuedTo) {
            $transformedIssuance['issuedToUser'] = $issuance->issuedTo;
        } else {
            $fallbackDepartment = $issuance->deliveryReceipt?->requestedByUser?->department?->DepartmentName ?? null;
            $transformedIssuance['issuedToUser'] = [
                'FullName' => $issuance->IssuedToName ?? 'External Recipient',
                'department' => $fallbackDepartment ? ['DepartmentName' => $fallbackDepartment] : null
            ];
        }

        // Transform details
        $transformedIssuance['details'] = $issuance->details->map(function ($detail) use ($issuance) {
            $inventoryDetail = $detail->inventoryDetail;
            $documentType = (string) ($issuance->DocumentType ?? 'ICS');

            $itemName = $this->resolveIssuanceItemName(
                $documentType,
                $inventoryDetail?->drDetail,
                $detail->ItemDescription
            );

            // Technical/long description comes from inventory detail Description.
            $technicalDescription = $inventoryDetail?->Description ?? null;

            $unitName = $inventoryDetail?->unitOfMeasure?->UnitName ?? $detail->Unit ?? null;

            $propertyType = $inventoryDetail?->propertyType;
            $propertyClassification = $propertyType?->propertyClassification;

            return [
                'IssuanceDetailID' => $detail->IssuanceDetailID,
                'IssuanceID' => $detail->IssuanceID,
                'RequestDetailID' => $detail->RequestDetailID,
                'ItemDescription' => $detail->ItemDescription,
                'Unit' => $detail->Unit,
                'InventoryDetailID' => $detail->InventoryDetailID,
                'QuantityIssued' => $detail->QuantityIssued,
                'Remarks' => $detail->Remarks,
                'inventoryDetail' => [
                    'InventoryDetailID' => $inventoryDetail?->InventoryDetailID,
                    'InventoryID' => $inventoryDetail?->InventoryID,
                    'PropertyNumber' => $inventoryDetail?->PropertyNumber,
                    'UnitCost' => $inventoryDetail?->UnitCost,
                    'propertyType' => $propertyType ? [
                        'PropertyTypeID' => $propertyType->PropertyTypeID,
                        'PropertyName' => $propertyType->PropertyName,
                        'PropertyClassificationID' => $propertyType->PropertyClassificationID,
                        'propertyClassification' => $propertyClassification ? [
                            'PropertyClassificationID' => $propertyClassification->PropertyClassificationID,
                            'ClassificationName' => $propertyClassification->ClassificationName,
                            'DocumentType' => $propertyClassification->DocumentType,
                        ] : null,
                    ] : null,
                    'unitOfMeasure' => $unitName ? ['UnitName' => $unitName] : null,
                    'qrCode' => $inventoryDetail?->qrCode ? [
                        'QRCodeID' => $inventoryDetail->qrCode->QRCodeID,
                        'PropertyID' => $inventoryDetail->qrCode->PropertyID,
                        'InventoryDetailID' => $inventoryDetail->qrCode->InventoryDetailID,
                        'DepartmentID' => $inventoryDetail->qrCode->DepartmentID,
                        'QRCodeData' => $inventoryDetail->qrCode->QRCodeData,
                        'DateGenerated' => $inventoryDetail->qrCode->DateGenerated,
                    ] : null,
                    'propertyInventory' => [
                        'ItemName' => $itemName,
                        'ItemDescription' => $technicalDescription,
                        'Brand' => $inventoryDetail?->Brand,
                        'ModelNumber' => $inventoryDetail?->ModelNumber,
                        'SerialNumber' => $inventoryDetail?->SerialNumber,
                    ],
                ],
            ];
        });

        $relatedIssuancesQuery = PropertyIssuance::query()
            ->select('IssuanceID', 'DocumentType', 'DocumentNumber', 'Status');

        if (!empty($issuance->RequestID)) {
            $relatedIssuancesQuery->where('RequestID', (int) $issuance->RequestID);
        } elseif (!empty($issuance->DRID)) {
            $relatedIssuancesQuery->where('DRID', (int) $issuance->DRID);
        }

        $transformedIssuance['issuances_by_document_type'] = $relatedIssuancesQuery->get()->mapWithKeys(function ($row) {
            $documentType = $row->DocumentType ?: 'ICS';

            return [
                $documentType => [
                    'IssuanceID' => (int) $row->IssuanceID,
                    'DocumentType' => $row->DocumentType,
                    'DocumentNumber' => $row->DocumentNumber,
                    'Status' => $row->Status,
                ],
            ];
        });

        return response()->json($transformedIssuance);
    }

    /**
     * Create new property issuance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'nullable|exists:assetrequest,RequestID',
            'dr_id' => 'nullable|exists:deliveryreceipt,DRID',
            'date_issued' => 'required|date',
            'remarks' => 'nullable|string',
            'issuerSignature' => 'nullable|string', // base64 data URL
            'issuerSignatureSource' => 'nullable|in:canvas,upload',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.request_detail_id' => 'nullable|integer',
            'inventory_items.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'inventory_items.*.quantity_issued' => 'required|numeric|min:1',
        ]);

        if (empty($validated['request_id']) && empty($validated['dr_id'])) {
            return response()->json([
                'message' => 'Either request_id or dr_id is required for issuance.',
                'error' => 'missing_source'
            ], 422);
        }

        $issuerSignature = $validated['issuerSignature'] ?? null;
        $issuerSignatureSource = $validated['issuerSignatureSource'] ?? null;

        if (($issuerSignature && !$issuerSignatureSource) || (!$issuerSignature && $issuerSignatureSource)) {
            return response()->json([
                'message' => 'Issuer signature source is required when a signature is provided.',
                'error' => 'invalid_signature'
            ], 422);
        }

        try {
            DB::beginTransaction();

            if (!empty($validated['request_id'])) {
                foreach ($validated['inventory_items'] as $item) {
                    if (empty($item['request_detail_id'])) {
                        return response()->json([
                            'message' => 'request_detail_id is required for request-based issuance items.',
                            'error' => 'missing_request_detail_id'
                        ], 422);
                    }
                }

            // Map request details to inventory items
            $inventoryMap = [];
            foreach ($validated['inventory_items'] as $item) {
                if (!isset($inventoryMap[$item['request_detail_id']])) {
                    $inventoryMap[$item['request_detail_id']] = [];
                }
                $inventoryMap[$item['request_detail_id']][] = $item;
            }

            // Get the asset request with its details
            $assetRequest = AssetRequest::with(['details', 'requester'])->findOrFail($validated['request_id']);

            // Ensure RIS exists. If not, create it automatically so issuance can proceed.
            $ris = RequisitionIssueSlip::where('asset_request_id', (int) $assetRequest->RequestID)->first();
            if (!$ris) {
                $ris = $this->createRisForAssetRequest($assetRequest);
            }
            if (in_array($ris->status, ['cancelled'])) {
                return response()->json([
                    'message' => 'Cannot issue: RIS is cancelled.',
                    'error' => 'ris_cancelled'
                ], 422);
            }

            $risDetailIds = $ris->items()->pluck('asset_request_detail_id')->map(function ($id) {
                return (int) $id;
            })->values()->all();

            $risItemsByDetail = RequisitionIssueSlipItem::where('ris_id', (int) $ris->id)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->get()
                ->groupBy('asset_request_detail_id');

            $consumedRisItemIds = [];

            if (empty($risDetailIds)) {
                return response()->json([
                    'message' => 'RIS has no approved items for issuance.',
                    'error' => 'ris_empty'
                ], 422);
            }

            $invalidIssuanceDetails = array_filter(array_keys($inventoryMap), function ($detailId) use ($risDetailIds) {
                return !in_array((int) $detailId, $risDetailIds, true);
            });

            if (!empty($invalidIssuanceDetails)) {
                return response()->json([
                    'message' => 'Issuance items must match RIS-approved items only.',
                    'error' => 'invalid_ris_items',
                    'invalid_request_detail_ids' => array_values($invalidIssuanceDetails)
                ], 422);
            }

            // Verify the request is approved / waiting for issuance
            if (!in_array($assetRequest->Status, ['approved', 'waiting_for_issuance'], true)) {
                return response()->json([
                    'message' => 'Only approved requests (waiting for issuance) can be issued',
                    'error' => 'Invalid request status'
                ], 422);
            }

            // Group items by recipient and classification
            // - PAR: group by assigned end user (ID or Name)
            // - ICS: always group to the request requester (dept head)
            $groupedItems = $assetRequest->details
                ->filter(function ($detail) use ($risDetailIds) {
                    return in_array((int) $detail->RequestDetailID, $risDetailIds, true);
                })
                ->groupBy(function ($detail) use ($assetRequest) {
                $classification = $detail->PropertyClassification ?? 'ICS';

                if ($classification === 'ICS') {
                    $requesterId = $assetRequest->RequestedBy;
                    return 'REQ_' . ($requesterId ?? 'UNKNOWN') . '_ICS';
                }

                $userKey = $detail->AssignedEndUserID
                    ? 'ID_' . $detail->AssignedEndUserID
                    : 'NAME_' . ($detail->IntendedEndUser ?? 'Unknown');

                return $userKey . '_' . $classification;
            });

            $issuances = [];

            foreach ($groupedItems as $key => $items) {
                $firstItem = $items->first();
                $endUserId = $firstItem->AssignedEndUserID;
                $endUserName = $firstItem->IntendedEndUser;
                $classification = $firstItem->PropertyClassification ?? 'ICS';

                $resolvedEndUser = $this->resolveEndUserForIssuance($classification, $endUserId, $endUserName, $assetRequest);
                $resolvedDepartmentId = (int) ($resolvedEndUser->DepartmentID ?? $assetRequest->DepartmentID);

                // Generate document number
                $documentNumber = $this->generateDocumentNumber($classification);

                // Prepare issuance data
                $issuanceData = [
                    'RequestID' => $validated['request_id'],
                    'IssuedBy' => Auth::id(),
                    'IssuerSignature' => $issuerSignature,
                    'IssuerSignatureSource' => $issuerSignatureSource,
                    'DocumentType' => $classification,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $validated['date_issued'],
                    'Remarks' => $validated['remarks'] ?? null,
                    'Status' => 'pending_acknowledgment',
                    'CreatedDate' => now(),
                ];

                // IMPORTANT: `propertyissuance.IssuedTo` must reference `enduser.EndUserID`.
                // Using `assetrequest.RequestedBy` (a `user.UserID`) will break legacy FK constraints
                // when persisting ICS/PAR records.
                $issuanceData['IssuedTo'] = (int) $resolvedEndUser->EndUserID;
                $issuanceData['IssuedToName'] = $resolvedEndUser->FullName ?? ($endUserName ?? $assetRequest->requester?->FullName ?? null);

                // Create the property issuance
                $issuance = PropertyIssuance::create($issuanceData);

                // Create issuance details from asset request details
                foreach ($items as $detail) {
                    $detailHasInventory = false;
                    
                    if (isset($inventoryMap[$detail->RequestDetailID])) {
                        // Create one issuance detail per assigned inventory item
                        foreach ($inventoryMap[$detail->RequestDetailID] as $inventoryItem) {
                            $qtyIssued = $inventoryItem['quantity_issued'];
                            $inventoryDetailId = (int) $inventoryItem['inventory_detail_id'];

                            $risCandidates = collect($risItemsByDetail->get((int) $detail->RequestDetailID, collect()));
                            $selectedRisItem = $risCandidates->first(function ($risItem) use ($inventoryDetailId, $consumedRisItemIds) {
                                return
                                    !isset($consumedRisItemIds[(int) $risItem->id])
                                    && (int) ($risItem->inventory_detail_id ?? 0) === $inventoryDetailId;
                            })
                                ?? $risCandidates->first(function ($risItem) use ($consumedRisItemIds) {
                                    return
                                        !isset($consumedRisItemIds[(int) $risItem->id])
                                        && empty($risItem->inventory_detail_id);
                                })
                                ?? $risCandidates->first(function ($risItem) use ($consumedRisItemIds) {
                                    return !isset($consumedRisItemIds[(int) $risItem->id]);
                                });

                            if (!$selectedRisItem) {
                                throw new \Exception('Unable to resolve RIS line for request detail ' . $detail->RequestDetailID . '.');
                            }

                            $consumedRisItemIds[(int) $selectedRisItem->id] = true;

                            if ((int) $qtyIssued !== 1) {
                                throw new \Exception('Issuance quantities must be 1 per InventoryDetailID for QR/accountability tracking. InventoryDetailID ' . $inventoryItem['inventory_detail_id'] . ' has quantity_issued=' . $qtyIssued);
                            }

                            PropertyIssuanceDetail::create([
                                'IssuanceID' => $issuance->IssuanceID,
                                'RequestDetailID' => $detail->RequestDetailID,
                                'ris_item_id' => (int) $selectedRisItem->id,
                                'InventoryDetailID' => $inventoryDetailId,
                                'ItemDescription' => $detail->ItemDescription,
                                'Unit' => $detail->Unit,
                                'QuantityIssued' => $qtyIssued, // 1 per item usually
                                'Remarks' => $detail->AdminNotes,
                            ]);
                            $detailHasInventory = true;

                            // Update Inventory Status
                            // Lock the inventory row to prevent concurrent allocation races
                            $invDetail = PropertyInventoryDetail::where('InventoryDetailID', $inventoryDetailId)->lockForUpdate()->first();
                            if ($invDetail) {
                                // Decrease available quantity
                                $invDetail->decrement('QuantityAvailable', $qtyIssued);
                                
                                // If no more available quantity, mark as Assigned
                                if ($invDetail->fresh()->QuantityAvailable <= 0) {
                                     $invDetail->update(['PropertyStatusID' => 2]); // 2 = Assigned
                                }

                                // Create/update AssignedProperty (accountability) and generate QR
                                $assignedProperty = AssignedProperty::where('InventoryDetailID', $invDetail->InventoryDetailID)->first();

                                $assignedPayload = [
                                    'InventoryDetailID' => $invDetail->InventoryDetailID,
                                    'EndUserID' => (int) $resolvedEndUser->EndUserID,
                                    'DepartmentID' => $resolvedDepartmentId,
                                    'PropertyCardNumber' => $issuance->DocumentNumber,
                                    'Description' => $invDetail->Description ?? $detail->ItemDescription,
                                    'AssignedDate' => $validated['date_issued'],
                                    'PropertyStatusID' => 2, // Assigned
                                    'ConditionID' => (int) ($invDetail->ConditionID ?? 1),
                                    'Remarks' => $validated['remarks'] ?? null,
                                ];

                                if ($assignedProperty) {
                                    $assignedProperty->fill($assignedPayload);
                                    $assignedProperty->save();
                                } else {
                                    $assignedProperty = AssignedProperty::create($assignedPayload);
                                }

                                $this->ensureQrCodeForDetail($invDetail, $assignedProperty);

                                $actorId = (int) (Auth::id() ?? 0);
                                if ($actorId > 0) {
                                    AssetAuditLog::createLog(
                                        'inventory_detail',
                                        (int) $invDetail->InventoryDetailID,
                                        'issued',
                                        $actorId,
                                        [
                                            'issuance_id' => (int) $issuance->IssuanceID,
                                            'document_number' => $issuance->DocumentNumber,
                                            'document_type' => $issuance->DocumentType,
                                            'issued_to' => $resolvedEndUser->FullName ?? null,
                                            'request_id' => (int) $assetRequest->RequestID,
                                        ]
                                    );
                                }

                                if (empty($selectedRisItem->inventory_detail_id)) {
                                    $selectedRisItem->inventory_detail_id = $invDetail->InventoryDetailID;
                                    $selectedRisItem->stock_no = trim((string) ($invDetail->PropertyNumber ?? '')) ?: $selectedRisItem->stock_no;
                                    $selectedRisItem->save();
                                }
                            }
                        }
                    }

                    // Fallback if no inventory map provided (legacy support or partial issuance?)
                    // For now, let's only create if we have inventory items. 
                    // If we strictly require inventory_items, we can skip the fallback or throw error.
                    // But since we validated required array, we assume we have them.
                    
                    // If no inventory was assigned for this detail (but it was in the request), 
                    // we might want to flag or skip. 
                    // Given the logic, we only issue what is assigned.
                }

                $issuances[] = $issuance;
            }

            // Update the request status to issued
            $assetRequest->update([
                'Status' => 'issued',
                'IssuedBy' => Auth::id(),
                'IssuedAt' => now()
            ]);

            // Sync RIS issuance metadata (single source of truth for RIS form)
            $ris->issued_by_user_id = (int) Auth::id();
            $ris->issued_at = now();
            $ris->status = 'issued';
            $ris->save();

            // Update RIS line issued quantities using direct issuance detail linkage.
            $issuedByRisItem = DB::table('propertyissuancedetail as pid')
                ->join('propertyissuance as pi', 'pid.IssuanceID', '=', 'pi.IssuanceID')
                ->whereNull('pid.deleted_at')
                ->whereNull('pi.deleted_at')
                ->where('pi.RequestID', (int) $assetRequest->RequestID)
                ->whereNotNull('pid.ris_item_id')
                ->groupBy('pid.ris_item_id')
                ->selectRaw('pid.ris_item_id as ris_item_id, SUM(pid.QuantityIssued) as qty_issued')
                ->pluck('qty_issued', 'ris_item_id');

            $issuedByRequestDetailFallback = DB::table('propertyissuancedetail as pid')
                ->join('propertyissuance as pi', 'pid.IssuanceID', '=', 'pi.IssuanceID')
                ->whereNull('pid.deleted_at')
                ->whereNull('pi.deleted_at')
                ->where('pi.RequestID', (int) $assetRequest->RequestID)
                ->groupBy('pid.RequestDetailID')
                ->selectRaw('pid.RequestDetailID as request_detail_id, SUM(pid.QuantityIssued) as qty_issued')
                ->pluck('qty_issued', 'request_detail_id');

            $risItems = RequisitionIssueSlipItem::where('ris_id', (int) $ris->id)->get();
            foreach ($risItems as $risItem) {
                $qtyIssued = isset($issuedByRisItem[(int) $risItem->id])
                    ? (int) $issuedByRisItem[(int) $risItem->id]
                    : (int) ($issuedByRequestDetailFallback[(int) $risItem->asset_request_detail_id] ?? 0);
                $qtyReq = (int) $risItem->quantity_requested;

                $risItem->quantity_issued = $qtyIssued;
                $risItem->issue_remarks = $qtyIssued >= $qtyReq && $qtyReq > 0
                    ? 'Issued in full'
                    : ($qtyIssued > 0 ? 'Partially issued' : ($risItem->issue_remarks ?: 'For issuance'));
                $risItem->save();
            }

            // Create audit log
            AssetAuditLog::create([
                'EntityType' => 'asset_request',
                'EntityID' => $assetRequest->RequestID,
                'Action' => 'issued',
                'PerformedBy' => Auth::id(),
                'Details' => json_encode([
                    'request_number' => $assetRequest->RequestNumber,
                    'issuances_count' => count($issuances),
                    'date_issued' => $validated['date_issued']
                ]),
                'IPAddress' => $request->ip(),
                'UserAgent' => $request->userAgent(),
            ]);

            // Process issuer signature
            if ($issuerSignature && $issuerSignatureSource) {
                $signatureService = new AssetRequestSignatureService();
                $signatureService->processAndStore(
                    $validated['request_id'],
                    'issuer',
                    $issuerSignature,
                    $issuerSignatureSource,
                    Auth::id()
                );
            }

            DB::commit();

            app(RoleNotificationService::class)->notifyRoles(
                ['Admin', 'GSO Staff'],
                'asset_request.issued',
                'Assets Issued',
                sprintf('Request %s has been issued and is awaiting acknowledgment.', $assetRequest->RequestNumber),
                [
                    'request_id' => (string) $assetRequest->RequestID,
                    'request_number' => (string) $assetRequest->RequestNumber,
                    'status' => 'issued',
                    'link' => '/admin/issuance',
                ],
                (int) (Auth::id() ?? 0)
            );

            if ((int) $assetRequest->RequestedBy > 0) {
                app(RoleNotificationService::class)->notifyUser(
                    (int) $assetRequest->RequestedBy,
                    'asset_request.issued',
                    'Your Request Has Been Issued',
                    sprintf('Request %s has been issued by GSO.', $assetRequest->RequestNumber),
                    [
                        'request_id' => (string) $assetRequest->RequestID,
                        'request_number' => (string) $assetRequest->RequestNumber,
                        'status' => 'issued',
                        'link' => '/department_head/issuance',
                    ]
                );
            }

            return response()->json([
                'message' => 'Assets issued successfully',
                'data' => [
                    'issuances' => collect($issuances)->map(function ($issuance) {
                        return $issuance->load([
                            'request',
                            'issuedBy:UserID,FullName',
                            'issuedTo:EndUserID,FullName',
                            // include inventoryDetail with PropertyNumber so client can show assigned numbers
                            'details.inventoryDetail:InventoryDetailID,Description,Brand,ModelNumber,PropertyNumber,SerialNumber,UnitCost',
                            'details'
                        ]);
                    })
                ]
            ], 201);

            }

            // Delivery Receipt-based issuance (no Asset Request / RIS)
            $deliveryReceipt = DeliveryReceipt::with(['requestedByUser:UserID,FullName,DepartmentID', 'requestedByUser.department:DepartmentID,DepartmentName'])
                ->findOrFail((int) $validated['dr_id']);
            $resolvedEndUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);

            $iar = InspectionAcceptanceReport::with(['details'])->where('DRID', (int) $deliveryReceipt->DRID)->first();
            if ($iar) {
                if (!PropertyInventory::where('IARID', $iar->IARID)->exists()) {
                    return response()->json([
                        'message' => 'Cannot issue yet. Inventory and property numbers are generated during inspection. Please complete inspection first.',
                        'error' => 'inventory_not_ready'
                    ], 409);
                }

                $issueDate = $validated['date_issued'];
                $requestedBy = (int) ($deliveryReceipt->RequestedBy ?? 0);

                if ($requestedBy > 0) {
                    $iar->AcceptedBy = $requestedBy;
                }

                $iar->DateAccepted = $issueDate;
                $iar->save();
            }

            $inventoryItems = collect($validated['inventory_items'])->map(function ($item) {
                return [
                    'inventory_detail_id' => (int) $item['inventory_detail_id'],
                    'quantity_issued' => (int) $item['quantity_issued'],
                ];
            });

            $inventoryDetailIds = $inventoryItems->pluck('inventory_detail_id')->unique()->values()->all();
            $inventoryDetails = PropertyInventoryDetail::with([
                    'drDetail.deliveryReceipt',
                    'propertyType.propertyClassification',
                    'unitOfMeasure',
                ])
                ->whereIn('InventoryDetailID', $inventoryDetailIds)
                ->lockForUpdate()
                ->get();

            if ($inventoryDetails->count() !== count($inventoryDetailIds)) {
                return response()->json([
                    'message' => 'One or more inventory items could not be found.',
                    'error' => 'invalid_inventory_items'
                ], 422);
            }

            foreach ($inventoryDetails as $detail) {
                $detailDrId = (int) ($detail->drDetail?->DRID ?? 0);
                if ($detailDrId !== (int) $deliveryReceipt->DRID) {
                    return response()->json([
                        'message' => 'All selected items must come from the same Delivery Receipt.',
                        'error' => 'mixed_delivery_receipts'
                    ], 422);
                }

                if ((int) $detail->PropertyStatusID !== 1) {
                    return response()->json([
                        'message' => 'Only available items can be issued.',
                        'error' => 'inventory_not_available'
                    ], 422);
                }
            }

            // Group items by document type (PAR/ICS)
            $groupedByType = $inventoryDetails->groupBy(function ($detail) {
                $docType = $detail->propertyType?->propertyClassification?->DocumentType ?? null;
                return $docType ?: 'ICS';
            });

            $issuances = [];

            foreach ($groupedByType as $documentType => $details) {
                $documentNumber = $this->generateDocumentNumber($documentType);

                $issuanceData = [
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id(),
                    'IssuerSignature' => $issuerSignature,
                    'IssuerSignatureSource' => $issuerSignatureSource,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $deliveryReceipt->RequestedBy ?? $resolvedEndUser->FullName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $validated['date_issued'],
                    'Remarks' => $validated['remarks'] ?? null,
                    'Status' => 'pending_acknowledgment',
                    'CreatedDate' => now(),
                ];

                $issuance = PropertyIssuance::create($issuanceData);

                foreach ($details as $detail) {
                    $requestedQty = (int) ($inventoryItems->firstWhere('inventory_detail_id', $detail->InventoryDetailID)['quantity_issued'] ?? 1);
                    if ($requestedQty !== 1) {
                        throw new \Exception('Issuance quantities must be 1 per InventoryDetailID for QR/accountability tracking.');
                    }

                    $itemDescription = $this->resolveIssuanceItemName(
                        $documentType,
                        $detail->drDetail,
                        $detail->Description
                    );
                    $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                    PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => null,
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'ItemDescription' => $itemDescription,
                        'Unit' => $unitName,
                        'QuantityIssued' => $requestedQty,
                        'Remarks' => $validated['remarks'] ?? null,
                    ]);

                    // Update inventory availability and status
                    $availableQty = (int) ($detail->QuantityAvailable ?? 0);
                    if ($availableQty < $requestedQty) {
                        throw new \Exception('Insufficient available quantity for selected inventory item.');
                    }
                    $detail->decrement('QuantityAvailable', $requestedQty);
                    if ($detail->fresh()->QuantityAvailable <= 0) {
                        $detail->update(['PropertyStatusID' => 2]);
                    }

                    // Create/update AssignedProperty for accountability
                    $assignedProperty = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->first();
                    $assignedPayload = [
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'EndUserID' => (int) $resolvedEndUser->EndUserID,
                        'DepartmentID' => (int) $deliveryReceipt->DepartmentID,
                        'PropertyCardNumber' => $issuance->DocumentNumber,
                        'Description' => $detail->Description ?? $itemDescription,
                        'AssignedDate' => $validated['date_issued'],
                        'PropertyStatusID' => 2,
                        'ConditionID' => (int) ($detail->ConditionID ?? 1),
                        'Remarks' => $validated['remarks'] ?? null,
                    ];

                    if ($assignedProperty) {
                        $assignedProperty->fill($assignedPayload);
                        $assignedProperty->save();
                    } else {
                        $assignedProperty = AssignedProperty::create($assignedPayload);
                    }

                    $this->ensureQrCodeForDetail($detail, $assignedProperty);

                    $actorId = (int) (Auth::id() ?? 0);
                    if ($actorId > 0) {
                        AssetAuditLog::createLog(
                            'inventory_detail',
                            (int) $detail->InventoryDetailID,
                            'issued',
                            $actorId,
                            [
                                'issuance_id' => (int) $issuance->IssuanceID,
                                'document_number' => $issuance->DocumentNumber,
                                'document_type' => $issuance->DocumentType,
                                'issued_to' => $resolvedEndUser->FullName ?? null,
                                'dr_id' => (int) $deliveryReceipt->DRID,
                            ]
                        );
                    }
                }

                $issuances[] = $issuance;
            }

            DB::commit();

            app(RoleNotificationService::class)->notifyUser(
                (int) $deliveryReceipt->RequestedBy,
                'delivery_receipt.issued',
                'Delivery Receipt Issued',
                sprintf('Your delivery receipt %s is now issued to department custody.', $deliveryReceipt->DRNumber),
                [
                    'dr_id' => (string) $deliveryReceipt->DRID,
                    'dr_number' => (string) $deliveryReceipt->DRNumber,
                    'status' => 'issued',
                    'link' => '/department_head/issuance',
                ]
            );

            return response()->json([
                'message' => 'Assets issued successfully',
                'data' => [
                    'issuances' => collect($issuances)->map(function ($issuance) {
                        return $issuance->load([
                            'issuedBy:UserID,FullName',
                            'issuedTo:EndUserID,FullName',
                            'deliveryReceipt',
                            'details.inventoryDetail:InventoryDetailID,Description,Brand,ModelNumber,PropertyNumber,SerialNumber,UnitCost',
                            'details'
                        ]);
                    })
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create property issuance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function ingestIarToInventory(InspectionAcceptanceReport $iar): void
    {
        if ($iar->details->isEmpty()) {
            throw new \Exception('No IAR details to ingest.');
        }

        $inventory = PropertyInventory::create([
            'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $iar->IARID,
            'IARID' => $iar->IARID,
            'DateReceived' => $iar->DateAccepted ?? now(),
            'Remarks' => trim((string) ($iar->Remarks ?? '')) !== ''
                ? trim((string) $iar->Remarks)
                : 'Auto-ingested from IAR #' . $iar->IARNumber,
            'CreatedDate' => now(),
        ]);

        $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'To be issued')
            ->orWhere('StatusName', 'For issuance')
            ->orWhere('StatusName', 'For Issuance')
            ->value('PropertyStatusID');

        if (!$toBeIssuedStatusId) {
            $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'Available')
                ->value('PropertyStatusID') ?? 1;
        }

        foreach ($iar->details as $detail) {
            $acceptedQty = (int) ($detail->QuantityAccepted ?? 0);
            if ($acceptedQty <= 0) {
                continue;
            }

            $drDetail = DeliveryReceiptDetail::find($detail->DRDetailID);
            if (!$drDetail) {
                continue;
            }

            $brand = $detail->VerifiedBrand ?? $drDetail->Brand;
            $model = $detail->VerifiedModel ?? $drDetail->ModelNumber;
            $serial = $detail->NoSerial ? null : ($detail->VerifiedSerial ?? $drDetail->SerialNumber);

            $propertyNumbers = $this->reservePropertyNumbers($acceptedQty);

            foreach ($propertyNumbers as $propertyNumber) {

                PropertyInventoryDetail::create([
                    'InventoryID' => $inventory->InventoryID,
                    'PropertyTypeID' => $detail->PropertyTypeID,
                    'PropertyNumber' => $propertyNumber,
                    'ConditionID' => $detail->ConditionID,
                    'Brand' => $brand,
                    'ModelNumber' => $model,
                    'SerialNumber' => $serial,
                    'Description' => $detail->Remarks,
                    'Quantity' => 1,
                    'QuantityAvailable' => 1,
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'UnitOfMeasureID' => $drDetail->UnitOfMeasureID,
                    'UnitCost' => $drDetail->UnitCost,
                    'TotalCost' => $drDetail->UnitCost,
                    'SourceIARDetailID' => $detail->IARDetailID,
                    'SourceDRDetailID' => $detail->DRDetailID,
                    'Remarks' => $detail->Remarks,
                    'CreatedDate' => now(),
                ]);
            }
        }
    }

    /**
     * Reserve a contiguous set of property numbers for the current year.
     *
     * @return array<int, string>
     */
    private function reservePropertyNumbers(int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        $year = (int) date('Y');
        $prefix = $year . '-';

        $latestItem = PropertyInventoryDetail::withTrashed()
            ->where('PropertyNumber', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByRaw("CAST(SUBSTRING_INDEX(PropertyNumber, '-', -1) AS UNSIGNED) DESC")
            ->orderBy('PropertyNumber', 'desc')
            ->first();

        $currentSequence = 0;
        if ($latestItem && preg_match('/-(\d+)$/', (string) $latestItem->PropertyNumber, $matches)) {
            $currentSequence = (int) $matches[1];
        }

        $numbers = [];
        for ($i = 0; $i < $count; $i++) {
            $currentSequence++;
            $numbers[] = $prefix . str_pad((string) $currentSequence, 6, '0', STR_PAD_LEFT);
        }

        return $numbers;
    }

    /**
     * Generate document number for PAR/ICS
     */
    private function generateDocumentNumber(string $type): string
    {
        $year = date('Y');
        $prefix = $type === 'PAR' ? "PAR-{$year}-" : "ICS-{$year}-";
        
        $lastIssuance = PropertyIssuance::where('DocumentType', $type)
            ->where('DocumentNumber', 'like', $prefix . '%')
            ->orderBy('IssuanceID', 'desc')
            ->first();

        if ($lastIssuance) {
            $lastNumber = (int) substr($lastIssuance->DocumentNumber, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function resolveEndUserForIssuance(string $classification, ?int $endUserId, ?string $endUserName, AssetRequest $assetRequest): EndUser
    {
        if ($classification === 'PAR' && $endUserId) {
            $endUser = EndUser::find($endUserId);
            if ($endUser) {
                return $endUser;
            }
        }

        $departmentId = (int) ($assetRequest->DepartmentID ?? ($assetRequest->requester?->DepartmentID ?? 0));
        $fallbackName = $assetRequest->requester?->FullName ?? null;
        $nameToUse = $endUserName ?: $fallbackName;

        if (!$departmentId || !$nameToUse) {
            throw new \Exception('Unable to resolve EndUser for issuance. Please set Assigned End User (PAR) or ensure requester has a FullName + Department.');
        }

        $existing = EndUser::where('FullName', $nameToUse)
            ->where('DepartmentID', $departmentId)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Auto-create EndUser for user-based recipients (e.g. ICS to requester)
        return EndUser::create([
            'PositionID' => 2, // Staff (safe default)
            'DepartmentID' => $departmentId,
            'FullName' => $nameToUse,
            'IsActive' => 1,
        ]);
    }

    private function buildQrPayloadForProperty(int $propertyId): string
    {
        $baseUrl = rtrim((string) config('gso.frontend_url'), '/');
        if ($baseUrl === '') {
            $baseUrl = 'http://localhost:3000';
        }

        $key = (string) config('app.key');
        $hash = substr(hash_hmac('sha256', (string) $propertyId, $key), 0, 12);

        return $baseUrl . '/qr/scan/' . $propertyId . '?h=' . $hash;
    }

    private function ensureQrCodeForDetail(PropertyInventoryDetail $detail, AssignedProperty $assignedProperty): void
    {
        $existing = QrCode::where('InventoryDetailID', $detail->InventoryDetailID)->first();
        if ($existing && !empty($existing->QRCodeData)) {
            return;
        }

        $qrPayload = $this->buildQrPayloadForProperty((int) $assignedProperty->PropertyID);

        QrCode::updateOrCreate(
            ['InventoryDetailID' => $detail->InventoryDetailID],
            [
                'PropertyID' => (int) $assignedProperty->PropertyID,
                'DepartmentID' => (int) $assignedProperty->DepartmentID,
                'QRCodeData' => $qrPayload,
                'DateGenerated' => now(),
            ]
        );
    }

    /**
     * Get approved asset requests for issuance
     */
    public function getApprovedRequests(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get approved requests that haven't been issued yet
            $query = AssetRequest::whereIn('Status', ['approved', 'waiting_for_issuance'])
                ->whereNotExists(function ($subquery) {
                    $subquery->select(DB::raw(1))
                        ->from('propertyissuance')
                        ->whereColumn('propertyissuance.RequestID', 'assetrequest.RequestID');
                })
                ->with([
                    'details.unitOfMeasure',
                    'requestor:UserID,FullName,Department',
                    'department'
                ]);

            // Role-based filtering
            if ($user && $user->RoleID === 3) { // Department Head
                $query->where('DepartmentID', $user->DepartmentID);
            }

            $requests = $query->orderBy('RequestDate', 'desc')->get();

            return response()->json([
                'data' => $requests,
                'message' => $requests->isEmpty() ? 'No approved requests available for issuance' : 'Approved requests retrieved successfully',
                'total' => $requests->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'message' => 'Error retrieving approved requests: ' . $e->getMessage(),
                'total' => 0,
                'error' => true
            ], 500);
        }
    }

    /**
     * Generate PAR/ICS document
     */
    public function generateDocument($id, $type)
    {
        $issuance = PropertyIssuance::with([
            'request.requester.userRole',
            'request.requester.department:DepartmentID,DepartmentName',
            'request.department:DepartmentID,DepartmentName',
            'issuedBy.userRole',
            'issuedTo.position',
            'details.inventoryDetail:InventoryDetailID,Description,Brand,ModelNumber,PropertyNumber,SerialNumber,UnitCost',
            'details.inventoryDetail.inventory',
        ])->findOrFail($id);

        $documentType = strtoupper($type);
        
        if (!in_array($documentType, ['PAR', 'ICS'])) {
            return response()->json(['message' => 'Invalid document type'], 400);
        }

        // Recipient rules:
        // - PAR: recipient is the assigned end user (issuedTo)
        // - ICS: recipient is the request requester (dept head)
        $recipientName = null;
        $recipientPosition = null;

        if ($documentType === 'ICS') {
            $recipientName = $issuance->request?->requester?->FullName;
            $recipientPosition = $issuance->request?->requester?->userRole?->RoleName
                ?? $issuance->request?->requester?->department?->DepartmentName
                ?? 'N/A';
        } else {
            $recipientName = $issuance->issuedTo?->FullName;
            $recipientPosition = $issuance->issuedTo?->position?->PositionName ?? 'N/A';
        }

        // Generate document data
        $documentData = [
            'type' => $documentType,
            'document_number' => $issuance->generateDocumentNumber($documentType),
            'date_issued' => $issuance->DateIssued,
            'department' => $issuance->request->department->DepartmentName,
            'recipient' => [
                'name' => $recipientName ?? $issuance->IssuedToName ?? 'N/A',
                'position' => $recipientPosition ?? 'N/A'
            ],
            'issuer' => [
                'name' => $issuance->issuedBy->FullName,
                'position' => $issuance->issuedBy->userRole?->RoleName ?? 'N/A'
            ],
            'items' => $issuance->details->map(function ($detail) {
                return [
                    'description' => $detail->inventoryDetail->ItemDescription,
                    'brand' => $detail->inventoryDetail->Brand,
                    'model' => $detail->inventoryDetail->ModelNumber,
                    'property_number' => $detail->inventoryDetail->PropertyNumber,
                    'serial_number' => $detail->inventoryDetail->SerialNumber,
                    'unit_cost' => $detail->inventoryDetail->UnitCost,
                    'quantity' => $detail->QuantityIssued,
                    'total_cost' => $detail->inventoryDetail->UnitCost * $detail->QuantityIssued
                ];
            }),
            'total_amount' => $issuance->details->sum(function ($detail) {
                return $detail->inventoryDetail->UnitCost * $detail->QuantityIssued;
            }),
            'remarks' => $issuance->Remarks
        ];

        return response()->json($documentData);
    }

    /**
     * Get issuance statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        $query = PropertyIssuance::query();

        // Role-based filtering
        if ($user->RoleID === 3) { // Department Head
            $query->whereHas('request', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        } elseif ($user->RoleID === 2) { // Staff
            $query->whereHas('issuedTo', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        }

        $total = $query->count();
        $thisMonth = $query->whereMonth('DateIssued', now()->month)
                          ->whereYear('DateIssued', now()->year)
                          ->count();
        
        $parCount = (clone $query)->where('DocumentType', 'PAR')->count();
        $icsCount = (clone $query)->where('DocumentType', 'ICS')->count();

        return response()->json([
            'total_issuances' => $total,
            'this_month' => $thisMonth,
            'par_count' => $parCount,
            'ics_count' => $icsCount,
        ]);
    }

    /**
     * Get approved requests ready for issuance
     */
    public function getRequestsForIssuance(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = AssetRequest::whereIn('Status', ['approved', 'waiting_for_issuance'])
                ->with([
                    'requester:UserID,FullName',
                    'department:DepartmentID,DepartmentName,DepartmentCode',
                    'details:RequestDetailID,RequestID,ItemDescription,Unit,QuantityRequested,Purpose,IntendedEndUser,PropertyClassification,AssignedEndUserID,AssignedDepartmentID'
                ]);

            // Role-based filtering
            if ($user && $user->RoleID === 3) { // Department Head
                $query->where('DepartmentID', $user->DepartmentID);
            }

            // Only get requests that haven't been fully issued yet
            $query->whereDoesntHave('propertyIssuance');

            $requests = $query->orderBy('RequestDate', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json($requests);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving requests for issuance: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Get delivery receipts that still have available inventory items for issuance
     */
    public function getDeliveryReceiptsForIssuance(Request $request)
    {
        try {
            // Simplified query - directly fetch delivery receipts without IAR dependencies
            $query = DB::table('deliveryreceipt')
                ->leftJoin('user as reqU', 'deliveryreceipt.RequestedBy', '=', 'reqU.UserID')
                ->leftJoin('department', 'reqU.DepartmentID', '=', 'department.DepartmentID')
                ->leftJoin('propertyissuance', 'propertyissuance.DRID', '=', 'deliveryreceipt.DRID')
                ->whereNull('deliveryreceipt.deleted_at')
                ->select(
                    'deliveryreceipt.DRID',
                    'deliveryreceipt.DRNumber',
                    'deliveryreceipt.IARNumber',
                    'reqU.FullName as RequestedBy',
                    'reqU.DepartmentID as DepartmentID',
                    'deliveryreceipt.DateReceived',
                    'deliveryreceipt.CreatedDate',
                    'deliveryreceipt.InspectionStatus',
                    'deliveryreceipt.IsPartial',
                    'deliveryreceipt.FinalizedAt',
                    'department.DepartmentName',
                    // Determine acceptance status based on propertyissuance records, not IAR
                    DB::raw('CASE WHEN MAX(propertyissuance.Status) IN ("acknowledged", "partially_acknowledged") THEN 1 ELSE 0 END as IsAccepted'),
                    DB::raw('COUNT(DISTINCT propertyissuance.IssuanceID) as IssuanceCount')
                )
                ->groupBy(
                    'deliveryreceipt.DRID',
                    'deliveryreceipt.DRNumber',
                    'deliveryreceipt.IARNumber',
                    'reqU.FullName',
                    'reqU.DepartmentID',
                    'deliveryreceipt.DateReceived',
                    'deliveryreceipt.CreatedDate',
                    'deliveryreceipt.InspectionStatus',
                    'deliveryreceipt.IsPartial',
                    'deliveryreceipt.FinalizedAt',
                    'department.DepartmentName'
                )
                ->orderBy('deliveryreceipt.CreatedDate', 'desc');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('deliveryreceipt.DRNumber', 'like', "%{$search}%")
                        ->orWhere('deliveryreceipt.IARNumber', 'like', "%{$search}%")
                        ->orWhere('reqU.FullName', 'like', "%{$search}%")
                        ->orWhere('department.DepartmentName', 'like', "%{$search}%");
                });
            }

            $perPage = (int) $request->get('per_page', 15);
            $results = $query->paginate($perPage);

            $results->getCollection()->transform(function ($row) {
                $row->items_by_document_type = $this->buildItemsByDocumentTypeForDr((int) $row->DRID);
                $isFinalized = !empty($row->FinalizedAt) || !((bool) ($row->IsPartial ?? false));
                $row->is_partial = ! $isFinalized;

                $issuancesByType = [
                    'PAR' => null,
                    'ICS' => null,
                ];

                $issuances = PropertyIssuance::query()
                    ->select('IssuanceID', 'DocumentType', 'DocumentNumber', 'Status')
                    ->where('DRID', (int) $row->DRID)
                    ->get();

                foreach ($issuances as $issuance) {
                    $documentType = $issuance->DocumentType ?: 'ICS';
                    if (!array_key_exists($documentType, $issuancesByType)) {
                        $issuancesByType[$documentType] = null;
                    }

                    $issuancesByType[$documentType] = [
                        'IssuanceID' => (int) $issuance->IssuanceID,
                        'DocumentType' => $issuance->DocumentType,
                        'DocumentNumber' => $issuance->DocumentNumber,
                        'Status' => $issuance->Status,
                    ];
                }

                $row->issuances_by_document_type = $issuancesByType;
                return $row;
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving delivery receipts for issuance: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    public function acknowledgeIarFromInspection(Request $request)
    {
        Log::info('Starting DR acknowledgment', ['request' => $request->all()]);

        $request->validate([
            'dr_id' => 'required|integer|exists:deliveryreceipt,DRID',
            'ris_number' => 'nullable|string|max:80|unique:requisition_issue_slips,ris_number',
        ]);

        $user = Auth::user();
        $roleId = (int) ($user->UserRoleID ?? 0);
        if (! in_array($roleId, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $drId = (int) $request->dr_id;
            $deliveryReceipt = DeliveryReceipt::with([
                'requestedByUser:UserID,FullName,DepartmentID',
                'requestedByUser.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->find($drId);
        
        if (! $deliveryReceipt) {
            return response()->json([
                'message' => 'Delivery receipt not found.',
            ], 404);
        }

        $resolvedEndUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);
        $isFinalized = !empty($deliveryReceipt->FinalizedAt) || !((bool) ($deliveryReceipt->IsPartial ?? false));
        $isPartial = ! $isFinalized;
        $targetStatus = $isPartial ? 'partially_acknowledged' : 'acknowledged';

        // Check if already acknowledged
        $existingFullyAcknowledged = PropertyIssuance::where('DRID', $deliveryReceipt->DRID)
            ->where('Status', 'acknowledged')
            ->first();

        if ($existingFullyAcknowledged) {
            return response()->json([
                'message' => 'Delivery receipt already fully acknowledged.',
            ], 409);
        }

        $existingPartiallyAcknowledged = PropertyIssuance::where('DRID', $deliveryReceipt->DRID)
            ->where('Status', 'partially_acknowledged')
            ->first();

        if ($existingPartiallyAcknowledged && $isPartial) {
            return response()->json([
                'message' => 'Delivery receipt is already partially acknowledged. Add remaining items or finalize the DR to complete issuance.',
            ], 409);
        }

        DB::beginTransaction();
        try {
            // Mark DR as inspected if not already
            if ($deliveryReceipt->InspectionStatus !== 'inspected') {
                $deliveryReceipt->InspectionStatus = 'inspected';
                $deliveryReceipt->InspectedBy = Auth::id();
                $deliveryReceipt->DateInspected = now();
                $deliveryReceipt->save();
            }

            // Create issuances directly from DR
            $issuances = $this->createIssuancesFromDeliveryReceipt($deliveryReceipt, $resolvedEndUser);
            $forcedRisNumber = trim((string) ($request->ris_number ?? '')) ?: null;
            $ris = $this->createRisFromDeliveryReceiptAcknowledgment($deliveryReceipt, $resolvedEndUser, $issuances, $forcedRisNumber);

            DB::commit();

            app(RoleNotificationService::class)->notifyUser(
                (int) $deliveryReceipt->RequestedBy,
                'delivery_receipt.acknowledged',
                $isPartial ? 'Delivery Receipt Partially Acknowledged' : 'Delivery Receipt Acknowledged',
                $isPartial
                    ? sprintf('Delivery receipt %s has been partially acknowledged and available inventory is ready.', $deliveryReceipt->DRNumber)
                    : sprintf('Delivery receipt %s has been acknowledged and inventory is ready.', $deliveryReceipt->DRNumber),
                [
                    'dr_id' => (string) $deliveryReceipt->DRID,
                    'dr_number' => (string) $deliveryReceipt->DRNumber,
                    'status' => $targetStatus,
                    'is_partial' => $isPartial,
                    'link' => '/department_head/delivery-receipts',
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in DR acknowledgment: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'Failed to acknowledge delivery receipt: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }

        return response()->json([
            'message' => 'Delivery receipt acknowledged and inventory updated',
            'data' => [
                'DRID' => (int) $deliveryReceipt->DRID,
                'issuances_created' => count($issuances ?? []),
                'ris_id' => $ris?->id,
                'ris_number' => $ris?->ris_number,
            ]
        ]);
    }

    public function ensureIssuancesForInspection(Request $request)
    {
        $request->validate([
            'dr_id' => 'nullable|integer|exists:deliveryreceipt,DRID',
            'iar_id' => 'nullable|integer|exists:inspectionacceptancereport,IARID',
        ]);

        if (! $request->filled('dr_id') && ! $request->filled('iar_id')) {
            return response()->json([
                'message' => 'dr_id or iar_id is required',
            ], 422);
        }

        $user = Auth::user();
        $roleId = (int) ($user->UserRoleID ?? 0);
        if (! in_array($roleId, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $drId = (int) $request->dr_id;
        if ($request->filled('iar_id')) {
            $iar = InspectionAcceptanceReport::findOrFail((int) $request->iar_id);
            $drId = (int) $iar->DRID;
        }

        $deliveryReceipt = DeliveryReceipt::with(['requestedByUser:UserID,FullName,DepartmentID'])
            ->find($drId);
        if (! $deliveryReceipt || ($deliveryReceipt->InspectionStatus ?? 'pending') !== 'inspected') {
            return response()->json([
                'message' => 'Delivery receipt is not inspected yet.',
            ], 409);
        }

        $resolvedEndUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);

        $issuances = $this->createPendingIssuancesFromDeliveryReceipt($deliveryReceipt, $resolvedEndUser);
        $issuancesByType = [
            'PAR' => null,
            'ICS' => null,
        ];

        foreach ($issuances as $issuance) {
            $docType = $issuance->DocumentType ?: 'ICS';
            if (!array_key_exists($docType, $issuancesByType)) {
                $issuancesByType[$docType] = null;
            }
            $issuancesByType[$docType] = [
                'IssuanceID' => (int) $issuance->IssuanceID,
                'DocumentType' => $issuance->DocumentType,
                'DocumentNumber' => $issuance->DocumentNumber,
                'Status' => $issuance->Status,
            ];
        }

        return response()->json([
            'message' => 'Issuances ready for printing',
            'data' => [
                'DRID' => (int) $deliveryReceipt->DRID,
                'issuances_by_document_type' => $issuancesByType,
                'items_by_document_type' => $this->buildItemsByDocumentTypeForDr((int) $deliveryReceipt->DRID),
            ],
        ]);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function buildItemsByDocumentTypeForDr(int $drId): array
    {
        $itemsByType = [
            'PAR' => [],
            'ICS' => [],
        ];

        $items = DB::table('deliveryreceiptdetail as drd')
            ->leftJoin('propertytype as pt', 'drd.PropertyTypeID', '=', 'pt.PropertyTypeID')
            ->leftJoin('propertyclassification as pc', 'pt.PropertyClassificationID', '=', 'pc.PropertyClassificationID')
            ->leftJoin('articles as a', 'drd.ArticleID', '=', 'a.ArticleID')
            ->where('drd.DRID', $drId)
            ->whereNull('drd.deleted_at')
            ->select(
                'drd.DRDetailID',
                'drd.QuantityDelivered as QuantityAccepted',
                DB::raw('COALESCE(a.ArticleName, drd.ItemDescription) as ItemDescription'),
                'drd.Brand',
                'drd.ModelNumber',
                'drd.SerialNumber',
                'pc.DocumentType'
            )
            ->get();

        foreach ($items as $item) {
            $docType = $item->DocumentType ?: 'ICS';
            if (! isset($itemsByType[$docType])) {
                $itemsByType[$docType] = [];
            }

            $itemsByType[$docType][] = [
                'DRDetailID' => $item->DRDetailID,
                'ItemDescription' => $item->ItemDescription,
                'Brand' => $item->Brand,
                'ModelNumber' => $item->ModelNumber,
                'SerialNumber' => $item->SerialNumber,
                'QuantityAccepted' => $item->QuantityAccepted,
                'DocumentType' => $docType,
            ];
        }

        return $itemsByType;
    }

    /**
     * Ensure we have an EndUser row that matches the DR requester.
     * If one does not exist, create a lightweight record so downstream issuance/assignment flows have a valid FK.
     */
    private function resolveEndUserForDeliveryReceipt(DeliveryReceipt $deliveryReceipt): EndUser
    {
        $requester = $deliveryReceipt->requestedByUser;
        if ($requester) {
            $existing = EndUser::where(function ($q) use ($requester) {
                    $hasCondition = false;

                    if (!empty($requester->Email)) {
                        $q->where('Email', $requester->Email);
                        $hasCondition = true;
                    }

                    if (!empty($requester->FullName)) {
                        if ($hasCondition) {
                            $q->orWhere('FullName', $requester->FullName);
                        } else {
                            $q->where('FullName', $requester->FullName);
                        }
                    }
                })
                ->when(!empty($requester->DepartmentID), function ($q) use ($requester) {
                    $q->where('DepartmentID', $requester->DepartmentID);
                })
                ->first();

            if ($existing) {
                return $existing;
            }

            return EndUser::create([
                'DepartmentID' => $requester->DepartmentID,
                'FullName' => $requester->FullName ?? 'Unknown Requester',
                'Email' => $requester->Email,
                'IsActive' => true,
                'PositionID' => 2,
                'ContactNumber' => null,
            ]);
        }

        // Fallback: create a generic recipient if the requester record is missing.
        return EndUser::create([
            'DepartmentID' => null,
            'FullName' => 'Unknown Requester',
            'Email' => null,
            'IsActive' => true,
            'PositionID' => 2,
            'ContactNumber' => null,
        ]);
    }

    private function createPendingIssuancesFromIar(InspectionAcceptanceReport $iar, DeliveryReceipt $deliveryReceipt, EndUser $resolvedEndUser): array
    {
        $iarDetailIds = $iar->details->pluck('IARDetailID')->filter()->values()->all();
        if (empty($iarDetailIds)) {
            return [];
        }

        $inventoryDetails = PropertyInventoryDetail::with([
                'drDetail',
                'propertyType.propertyClassification',
                'unitOfMeasure',
            ])
            ->whereIn('SourceIARDetailID', $iarDetailIds)
            ->get();

        // If inventory details were not created yet from inspection ingestion, fall back to using IAR details
        // so we can create pending issuance headers/details for printing immediately after inspection.
        $useIarDetailsFallback = $inventoryDetails->isEmpty();

        $existingIssuances = PropertyIssuance::with(['details'])
            ->where('DRID', (int) $deliveryReceipt->DRID)
            ->get()
            ->keyBy(function ($issuance) {
                return (string) $issuance->DocumentType;
            });

        if ($useIarDetailsFallback) {
            // Group by document type using the IARDetail -> PropertyType -> PropertyClassification -> DocumentType
            $groupedByType = collect($iar->details)->groupBy(function ($d) {
                $docType = $d->propertyType?->propertyClassification?->DocumentType ?? null;
                return $docType ?: 'ICS';
            });
        } else {
            $groupedByType = $inventoryDetails->groupBy(function ($detail) {
                $docType = $detail->propertyType?->propertyClassification?->DocumentType ?? null;
                return $docType ?: 'ICS';
            });
        }

        $issuances = [];
        $issuedToName = $deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? null;
        $issuedAt = $iar->DateInspected ?? now();

        foreach ($groupedByType as $documentType => $details) {
            $documentType = (string) $documentType;
            $issuance = $existingIssuances->get($documentType);

            if (! $issuance) {
                $documentNumber = $this->generateDocumentNumber($documentType);

                $issuance = PropertyIssuance::create([
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id(),
                    'IssuerSignature' => null,
                    'IssuerSignatureSource' => null,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $issuedToName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $issuedAt,
                    'Remarks' => 'Auto-created from inspection completion',
                    'Status' => 'pending_acknowledgment',
                    'AcknowledgedBy' => null,
                    'AcknowledgedAt' => null,
                    'CreatedDate' => now(),
                ]);
            }

            foreach ($details as $detail) {
                // When using IAR-detail fallback, the $detail will be an IARDetail (no InventoryDetailID).
                if (isset($detail->InventoryDetailID)) {
                    $alreadyExists = PropertyIssuanceDetail::where('IssuanceID', $issuance->IssuanceID)
                        ->where('InventoryDetailID', $detail->InventoryDetailID)
                        ->exists();

                    if ($alreadyExists) {
                        continue;
                    }

                    $itemDescription = $detail->drDetail?->ItemDescription ?? $detail->Description ?? 'Item';
                    $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                    PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => null,
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'ItemDescription' => $itemDescription,
                        'Unit' => $unitName,
                        'QuantityIssued' => 1,
                        'Remarks' => $issuance->Remarks,
                    ]);
                } else {
                    // fallback: create issuance detail from IARDetail (no inventory yet)
                    $qty = (int) ($detail->QuantityAccepted ?? 1);
                    $itemDescription = $this->resolveIssuanceItemName(
                        $documentType,
                        $detail->drDetail,
                        null
                    );

                    // Avoid duplicate entries with same IARDetail reference
                    $exists = PropertyIssuanceDetail::where('IssuanceID', $issuance->IssuanceID)
                        ->where('ItemDescription', $itemDescription)
                        ->whereNull('InventoryDetailID')
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => null,
                        'InventoryDetailID' => null,
                        'ItemDescription' => $itemDescription,
                        'Unit' => null,
                        'QuantityIssued' => $qty,
                        'Remarks' => $issuance->Remarks,
                    ]);
                }
            }

            $issuances[] = $issuance;
        }

        return $issuances;
    }

    private function createIssuancesFromIar(InspectionAcceptanceReport $iar, DeliveryReceipt $deliveryReceipt, EndUser $resolvedEndUser): array
    {
        $iarDetailIds = $iar->details->pluck('IARDetailID')->filter()->values()->all();
        if (empty($iarDetailIds)) {
            return [];
        }

        $inventoryDetails = PropertyInventoryDetail::with([
                'drDetail',
                'propertyType.propertyClassification',
                'unitOfMeasure',
            ])
            ->whereIn('SourceIARDetailID', $iarDetailIds)
            ->lockForUpdate()
            ->get();

        if ($inventoryDetails->isEmpty()) {
            return [];
        }

        $existingIssuances = PropertyIssuance::with(['details'])
            ->where('DRID', (int) $deliveryReceipt->DRID)
            ->get()
            ->keyBy(function ($issuance) {
                return (string) $issuance->DocumentType;
            });

        $groupedByType = $inventoryDetails->groupBy(function ($detail) {
            $docType = $detail->propertyType?->propertyClassification?->DocumentType ?? null;
            return $docType ?: 'ICS';
        });

        $issuances = [];
        $issuedToName = $deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? null;
        $departmentId = (int) ($deliveryReceipt->requestedByUser?->DepartmentID ?? 0) ?: null;
        $issuedAt = $iar->DateAccepted ?? now();
        $canStoreAcknowledgedBy = Schema::hasColumn('propertyissuance', 'AcknowledgedBy');
        $canStoreAcknowledgedAt = Schema::hasColumn('propertyissuance', 'AcknowledgedAt');

        foreach ($groupedByType as $documentType => $details) {
            $documentType = (string) $documentType;
            $issuance = $existingIssuances->get($documentType);

            if (! $issuance) {
                $documentNumber = $this->generateDocumentNumber($documentType);

                $issuanceData = [
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id(),
                    'IssuerSignature' => null,
                    'IssuerSignatureSource' => null,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $issuedToName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $issuedAt,
                    'Remarks' => 'Auto-created from IAR acknowledgement',
                    'Status' => 'acknowledged',
                    'CreatedDate' => now(),
                ];

                $issuance = PropertyIssuance::create($issuanceData);
                if ($canStoreAcknowledgedBy) {
                    $issuance->AcknowledgedBy = (int) ($deliveryReceipt->RequestedBy ?? 0) ?: null;
                }
                if ($canStoreAcknowledgedAt) {
                    $issuance->AcknowledgedAt = $issuedAt;
                }
                $issuance->Status = 'acknowledged';
                $issuance->save();

                $existingDetailIds = [];
            } else {
                if ($canStoreAcknowledgedBy) {
                    $issuance->AcknowledgedBy = (int) ($deliveryReceipt->RequestedBy ?? 0) ?: null;
                }
                if ($canStoreAcknowledgedAt) {
                    $issuance->AcknowledgedAt = $issuedAt;
                }
                $issuance->Status = 'acknowledged';
                $issuance->save();

                $existingDetailIds = $issuance->details
                    ->pluck('InventoryDetailID')
                    ->filter()
                    ->map(function ($id) {
                        return (int) $id;
                    })
                    ->values()
                    ->all();
            }

            foreach ($details as $detail) {
                $detailId = (int) $detail->InventoryDetailID;
                $detailExists = in_array($detailId, $existingDetailIds, true);

                $itemDescription = $this->resolveIssuanceItemName(
                    $documentType,
                    $detail->drDetail,
                    $detail->Description
                );
                $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                if (! $detailExists) {
                    PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => null,
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'ItemDescription' => $itemDescription,
                        'Unit' => $unitName,
                        'QuantityIssued' => 1,
                        'Remarks' => $issuance->Remarks,
                    ]);

                    $existingDetailIds[] = $detailId;
                }

                $assignedProperty = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->first();
                $availableQty = (int) ($detail->QuantityAvailable ?? 0);
                $shouldFinalize = $availableQty > 0 || (int) ($detail->PropertyStatusID ?? 0) !== 2;

                if ($shouldFinalize) {
                    if ($availableQty < 1) {
                        $alreadyAssigned = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->exists();
                        if ($alreadyAssigned) {
                            throw new \Exception('Insufficient available quantity for IAR issuance item.');
                        }

                        $detail->QuantityAvailable = 1;
                        $detail->save();
                        $availableQty = 1;
                    }

                    $detail->decrement('QuantityAvailable', 1);
                    if ($detail->fresh()->QuantityAvailable <= 0) {
                        $detail->update(['PropertyStatusID' => 2]);
                    }
                }

                $assignedPayload = [
                    'InventoryDetailID' => $detail->InventoryDetailID,
                    'EndUserID' => (int) $resolvedEndUser->EndUserID,
                    'DepartmentID' => $departmentId,
                    'PropertyCardNumber' => $issuance->DocumentNumber,
                    'Description' => $detail->Description ?? $itemDescription,
                    'AssignedDate' => $issuedAt,
                    'PropertyStatusID' => $shouldFinalize
                        ? 2
                        : (int) ($assignedProperty?->PropertyStatusID ?? 2),
                    'ConditionID' => (int) ($detail->ConditionID ?? 1),
                    'Remarks' => $issuance->Remarks,
                ];

                if ($assignedProperty) {
                    $assignedProperty->fill($assignedPayload);
                    $assignedProperty->save();
                } else {
                    $assignedProperty = AssignedProperty::create($assignedPayload);
                }

                $this->ensureQrCodeForDetail($detail, $assignedProperty);
            }

            $issuances[] = $issuance;
        }

        return $issuances;
    }

    /**
     * Create a synthetic RIS for delivery-receipt acknowledgments so RSMI can use a real RIS number.
     *
     * The DR flow does not originate from an Asset Request, so we create a lightweight request
     * and attach the issued rows to it to preserve the existing RIS/RSMI reporting contract.
     */
    private function createRisFromDeliveryReceiptAcknowledgment(DeliveryReceipt $deliveryReceipt, EndUser $resolvedEndUser, array $issuances, ?string $forcedRisNumber = null): ?RequisitionIssueSlip
    {
        $deliveryReceipt->loadMissing([
            'requestedByUser.department:DepartmentID,DepartmentName,DepartmentCode',
        ]);

        $issuanceCollection = collect($issuances)
            ->filter(fn ($issuance) => $issuance instanceof PropertyIssuance)
            ->values();

        if ($issuanceCollection->isEmpty()) {
            return null;
        }

        $issuedDetails = $issuanceCollection->flatMap(function (PropertyIssuance $issuance) {
            $issuance->loadMissing([
                'details.inventoryDetail.propertyType.propertyClassification',
                'details.inventoryDetail.unitOfMeasure',
            ]);

            return $issuance->details->map(function (PropertyIssuanceDetail $detail) use ($issuance) {
                return [
                    'issuance' => $issuance,
                    'detail' => $detail,
                ];
            });
        })->values();

        if ($issuedDetails->isEmpty()) {
            return null;
        }

        $syntheticRequestNumber = 'REQ-' . date('Y') . '-DR-' . str_pad((string) $deliveryReceipt->DRID, 4, '0', STR_PAD_LEFT);
        $syntheticPurpose = trim((string) ($deliveryReceipt->Remarks ?? '')) ?: sprintf('Auto-generated from delivery receipt %s acknowledgement', (string) $deliveryReceipt->DRNumber);
        $requesterUserId = (int) ($deliveryReceipt->RequestedBy ?? 0);
        $requesterDepartmentId = (int) ($deliveryReceipt->requestedByUser?->DepartmentID ?? $resolvedEndUser->DepartmentID ?? Auth::user()?->DepartmentID ?? 0) ?: null;
        $requesterDepartmentName = (string) ($deliveryReceipt->requestedByUser?->department?->DepartmentName ?? 'Delivery Receipt');
        $requesterDepartmentCode = (string) ($deliveryReceipt->requestedByUser?->department?->DepartmentCode ?? $requesterDepartmentId ?? 'N/A');
        $issuedAt = $deliveryReceipt->DateInspected ?? now();

        $syntheticRequest = AssetRequest::updateOrCreate(
            ['RequestNumber' => $syntheticRequestNumber],
            [
                'RequestedBy' => $requesterUserId > 0 ? $requesterUserId : (int) Auth::id(),
                'DepartmentID' => $requesterDepartmentId ?? (int) ($resolvedEndUser->DepartmentID ?? 0),
                'Purpose' => $syntheticPurpose,
                'Status' => 'issued',
                'ReviewedBy' => (int) Auth::id(),
                'ReviewedAt' => now(),
                'ReviewNotes' => 'Auto-generated from delivery receipt acknowledgment',
                'IssuedBy' => (int) Auth::id(),
                'IssuedAt' => now(),
                'RequestDate' => $issuedAt,
            ]
        );

        $existingRis = RequisitionIssueSlip::with('items')
            ->where('asset_request_id', (int) $syntheticRequest->RequestID)
            ->first();

        if ($existingRis) {
            return $existingRis;
        }

        // Use provided RIS number if given, otherwise fall back to auto-generation (legacy)
        if ($forcedRisNumber) {
            $risNumber = $forcedRisNumber;
        } else {
            $lastRisNumber = RequisitionIssueSlip::lockForUpdate()
                ->orderByDesc('id')
                ->value('ris_number');

            $nextSerial = 1;
            if ($lastRisNumber) {
                $parts = explode('-', (string) $lastRisNumber);
                if (count($parts) === 3) {
                    $nextSerial = (int) $parts[2] + 1;
                }
            }

            $risDate = now()->setTimezone('Asia/Manila');
            $risNumber = sprintf('%04d-%02d-%04d', (int) $risDate->year, (int) $risDate->month, $nextSerial);
        }

        $ris = RequisitionIssueSlip::create([
            'asset_request_id' => (int) $syntheticRequest->RequestID,
            'ris_number' => $risNumber,
            'entity_name' => (string) ($deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? 'Unknown Entity'),
            'fund_cluster' => 'AUTO',
            'division' => $requesterDepartmentName,
            'responsibility_center_code' => $requesterDepartmentCode,
            'purpose' => $syntheticPurpose,
            'status' => 'issued',
            'requested_by_user_id' => $requesterUserId > 0 ? $requesterUserId : null,
            'requested_at' => $issuedAt,
            'approved_by_user_id' => (int) Auth::id(),
            'approved_at' => now(),
            'prepared_by_user_id' => (int) Auth::id(),
            'prepared_at' => now(),
            'issued_by_user_id' => (int) Auth::id(),
            'issued_at' => now(),
            'remarks' => 'Auto-generated from delivery receipt acknowledgment',
        ]);

        foreach ($issuedDetails as $entry) {
            /** @var PropertyIssuance $issuance */
            $issuance = $entry['issuance'];
            /** @var PropertyIssuanceDetail $detail */
            $detail = $entry['detail'];
            $inventoryDetail = $detail->inventoryDetail;
            $quantityIssued = (int) ($detail->QuantityIssued ?? 1);

            $requestDetail = AssetRequestDetail::create([
                'RequestID' => (int) $syntheticRequest->RequestID,
                'PropertyInventoryDetailID' => $detail->InventoryDetailID,
                'ItemDescription' => (string) ($detail->ItemDescription ?? $inventoryDetail?->Description ?? 'Item'),
                'Unit' => (string) ($detail->Unit ?? $inventoryDetail?->unitOfMeasure?->UnitName ?? 'N/A'),
                'QuantityRequested' => $quantityIssued,
                'Purpose' => $syntheticPurpose,
                'IntendedEndUser' => $resolvedEndUser->FullName ?? null,
                'AdditionalNotes' => 'Auto-generated from delivery receipt acknowledgment',
                'PropertyClassification' => (string) ($inventoryDetail?->propertyType?->propertyClassification?->DocumentType ?? $issuance->DocumentType ?? 'ICS'),
                'AssignedEndUserID' => (int) $resolvedEndUser->EndUserID,
                'AssignedDepartmentID' => (int) ($resolvedEndUser->DepartmentID ?? $deliveryReceipt->requestedByUser?->DepartmentID ?? 0) ?: null,
                'AdminNotes' => 'Auto-generated from delivery receipt acknowledgment',
                'IsApprovedForIssuance' => true,
            ]);

            $risItem = RequisitionIssueSlipItem::create([
                'ris_id' => (int) $ris->id,
                'asset_request_detail_id' => (int) $requestDetail->RequestDetailID,
                'inventory_detail_id' => $detail->InventoryDetailID ? (int) $detail->InventoryDetailID : null,
                'stock_no' => trim((string) ($inventoryDetail?->PropertyNumber ?? '')) ?: 'N/A',
                'unit' => trim((string) ($detail->Unit ?? $inventoryDetail?->unitOfMeasure?->UnitName ?? '')) ?: 'N/A',
                'description' => trim((string) ($detail->ItemDescription ?? $inventoryDetail?->Description ?? 'Item')) ?: 'Item',
                'quantity_requested' => $quantityIssued,
                'stock_available' => true,
                'quantity_issued' => $quantityIssued,
                'issue_remarks' => $quantityIssued > 0 ? 'Issued in full' : 'For issuance',
            ]);

            $detail->update([
                'RequestDetailID' => (int) $requestDetail->RequestDetailID,
                'ris_item_id' => (int) $risItem->id,
            ]);
        }

        $issuanceCollection->each(function (PropertyIssuance $issuance) use ($syntheticRequest) {
            $issuance->update([
                'RequestID' => (int) $syntheticRequest->RequestID,
            ]);
        });

        return $ris;
    }

    /**
     * Create a RIS for an AssetRequest when none exists.
     */
    private function createRisForAssetRequest(AssetRequest $assetRequest): RequisitionIssueSlip
    {
        // generate ris number using same pattern: YYYY-MM-SSSS
        $lastRisNumber = RequisitionIssueSlip::lockForUpdate()
            ->orderByDesc('id')
            ->value('ris_number');

        $nextSerial = 1;
        if ($lastRisNumber) {
            $parts = explode('-', (string) $lastRisNumber);
            if (count($parts) === 3) {
                $nextSerial = (int) $parts[2] + 1;
            }
        }

        $risDate = now()->setTimezone('Asia/Manila');
        $risNumber = sprintf('%04d-%02d-%04d', (int) $risDate->year, (int) $risDate->month, $nextSerial);

        $requesterUserId = (int) ($assetRequest->RequestedBy ?? 0);
        $departmentId = (int) ($assetRequest->DepartmentID ?? 0) ?: null;
        $departmentName = (string) ($assetRequest->department?->DepartmentName ?? '');
        $departmentCode = (string) ($assetRequest->department?->DepartmentCode ?? $departmentId ?? 'N/A');

        $ris = RequisitionIssueSlip::create([
            'asset_request_id' => (int) $assetRequest->RequestID,
            'ris_number' => $risNumber,
            'entity_name' => (string) ($assetRequest->requester?->FullName ?? $departmentName ?? 'Request'),
            'fund_cluster' => 'AUTO',
            'division' => $departmentName,
            'responsibility_center_code' => $departmentCode,
            'purpose' => $assetRequest->Purpose ?? 'Auto-generated RIS',
            'status' => 'for_issuance',
            'requested_by_user_id' => $requesterUserId > 0 ? $requesterUserId : null,
            'requested_at' => $assetRequest->RequestDate ?? now(),
            'approved_by_user_id' => (int) Auth::id(),
            'approved_at' => now(),
            'prepared_by_user_id' => (int) Auth::id(),
            'prepared_at' => now(),
            'remarks' => 'Auto-generated by issuance flow',
        ]);

        // Add RIS items for approved request details (or include all if IsApprovedForIssuance not set)
        $details = $assetRequest->details()->whereNull('deleted_at')->get();
        foreach ($details as $detail) {
            // If IsApprovedForIssuance exists, only include approved lines
            if (isset($detail->IsApprovedForIssuance) && !$detail->IsApprovedForIssuance) {
                continue;
            }

            RequisitionIssueSlipItem::create([
                'ris_id' => (int) $ris->id,
                'asset_request_detail_id' => (int) $detail->RequestDetailID,
                'inventory_detail_id' => null,
                'stock_no' => trim((string) ($detail->PropertyInventoryDetailID ?? '')) ?: null,
                'unit' => trim((string) ($detail->Unit ?? '')) ?: 'N/A',
                'description' => trim((string) ($detail->ItemDescription ?? 'Item')) ?: 'Item',
                'quantity_requested' => (int) ($detail->QuantityRequested ?? 0),
                'stock_available' => true,
                'quantity_issued' => 0,
                'issue_remarks' => 'For issuance',
            ]);
        }

        return $ris;
    }

    private function createPendingIssuancesFromDeliveryReceipt(DeliveryReceipt $deliveryReceipt, EndUser $resolvedEndUser): array
    {
        $inventoryDetails = PropertyInventoryDetail::with([
                'drDetail',
                'propertyType.propertyClassification',
                'unitOfMeasure',
            ])
            ->whereHas('drDetail', function ($q) use ($deliveryReceipt) {
                $q->where('DRID', (int) $deliveryReceipt->DRID);
            })
            ->get();

        if ($inventoryDetails->isEmpty()) {
            return [];
        }

        $existingIssuances = PropertyIssuance::with(['details'])
            ->where('DRID', (int) $deliveryReceipt->DRID)
            ->get()
            ->keyBy(function ($issuance) {
                return (string) $issuance->DocumentType;
            });

        $groupedByType = $inventoryDetails->groupBy(function ($detail) {
            $docType = $detail->propertyType?->propertyClassification?->DocumentType ?? null;
            return $docType ?: 'ICS';
        });

        $issuances = [];
        $issuedToName = $deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? null;
        $issuedAt = $deliveryReceipt->DateInspected ?? $deliveryReceipt->DateReceived ?? now();

        foreach ($groupedByType as $documentType => $details) {
            $documentType = (string) $documentType;
            $issuance = $existingIssuances->get($documentType);

            if (! $issuance) {
                $documentNumber = $this->generateDocumentNumber($documentType);

                $issuance = PropertyIssuance::create([
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id(),
                    'IssuerSignature' => null,
                    'IssuerSignatureSource' => null,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $issuedToName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $issuedAt,
                    'Remarks' => 'Auto-created from DR inspection',
                    'Status' => 'pending_acknowledgment',
                    'AcknowledgedBy' => null,
                    'AcknowledgedAt' => null,
                    'CreatedDate' => now(),
                ]);
            }

            foreach ($details as $detail) {
                $alreadyExists = PropertyIssuanceDetail::where('IssuanceID', $issuance->IssuanceID)
                    ->where('InventoryDetailID', $detail->InventoryDetailID)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $itemDescription = $this->resolveIssuanceItemName(
                    $documentType,
                    $detail->drDetail,
                    $detail->Description
                );
                $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                PropertyIssuanceDetail::create([
                    'IssuanceID' => $issuance->IssuanceID,
                    'RequestDetailID' => null,
                    'InventoryDetailID' => $detail->InventoryDetailID,
                    'ItemDescription' => $itemDescription,
                    'Unit' => $unitName,
                    'QuantityIssued' => 1,
                    'Remarks' => $issuance->Remarks,
                ]);
            }

            $issuances[] = $issuance;
        }

        return $issuances;
    }

    private function createIssuancesFromDeliveryReceipt(DeliveryReceipt $deliveryReceipt, EndUser $resolvedEndUser): array
    {
        $this->ensureInventoryForDeliveryReceipt($deliveryReceipt);

        $inventoryDetails = PropertyInventoryDetail::with([
                'drDetail',
                'propertyType.propertyClassification',
                'unitOfMeasure',
            ])
            ->whereHas('drDetail', function ($q) use ($deliveryReceipt) {
                $q->where('DRID', (int) $deliveryReceipt->DRID);
            })
            ->lockForUpdate()
            ->get();

        if ($inventoryDetails->isEmpty()) {
            return [];
        }

        $existingIssuances = PropertyIssuance::with(['details'])
            ->where('DRID', (int) $deliveryReceipt->DRID)
            ->get()
            ->keyBy(function ($issuance) {
                return (string) $issuance->DocumentType;
            });

        $groupedByType = $inventoryDetails->groupBy(function ($detail) {
            $docType = $detail->propertyType?->propertyClassification?->DocumentType ?? null;
            return $docType ?: 'ICS';
        });

        $issuances = [];
        $issuedToName = $deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? null;
        $departmentId = (int) ($deliveryReceipt->requestedByUser?->DepartmentID ?? 0) ?: null;
        $issuedAt = now();
        $isFinalized = !empty($deliveryReceipt->FinalizedAt) || !((bool) ($deliveryReceipt->IsPartial ?? false));
        $isPartial = ! $isFinalized;
        $status = $isPartial ? 'partially_acknowledged' : 'acknowledged';

        foreach ($groupedByType as $documentType => $details) {
            $documentType = (string) $documentType;
            $issuance = $existingIssuances->get($documentType);

            if (! $issuance) {
                $documentNumber = $this->generateDocumentNumber($documentType);

                $issuanceData = [
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id(),
                    'IssuerSignature' => null,
                    'IssuerSignatureSource' => null,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $issuedToName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $issuedAt,
                    'Remarks' => 'Auto-created from DR acknowledgment',
                    'Status' => $status,
                    'AcknowledgedBy' => Auth::id(),
                    'AcknowledgedAt' => $issuedAt,
                    'CreatedDate' => now(),
                ];

                $issuance = PropertyIssuance::create($issuanceData);
                $existingDetailIds = [];
            } else {
                $issuance->AcknowledgedBy = Auth::id();
                $issuance->AcknowledgedAt = $issuedAt;
                $issuance->Status = $status;
                $issuance->save();

                $existingDetailIds = $issuance->details
                    ->pluck('InventoryDetailID')
                    ->filter()
                    ->map(function ($id) {
                        return (int) $id;
                    })
                    ->values()
                    ->all();
            }

            foreach ($details as $detail) {
                $inventoryDetailId = (int) $detail->InventoryDetailID;
                $detailExists = in_array($inventoryDetailId, $existingDetailIds, true);

                $itemDescription = $this->resolveIssuanceItemName(
                    $documentType,
                    $detail->drDetail,
                    $detail->Description
                );
                $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                if (! $detailExists) {
                    PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => null,
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'ItemDescription' => $itemDescription,
                        'Unit' => $unitName,
                        'QuantityIssued' => 1,
                        'Remarks' => $issuance->Remarks,
                    ]);

                    $existingDetailIds[] = $inventoryDetailId;
                }

                $assignedProperty = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->first();
                $availableQty = (int) ($detail->QuantityAvailable ?? 0);
                $shouldFinalize = $availableQty > 0 || (int) ($detail->PropertyStatusID ?? 0) !== 2;

                if ($shouldFinalize) {
                    if ($availableQty < 1) {
                        $alreadyAssigned = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->exists();
                        if ($alreadyAssigned) {
                            throw new \Exception('Insufficient available quantity for DR issuance item.');
                        }

                        $detail->QuantityAvailable = 1;
                        $detail->save();
                    }

                    $detail->decrement('QuantityAvailable', 1);
                    if ($detail->fresh()->QuantityAvailable <= 0) {
                        $detail->update(['PropertyStatusID' => 2]);
                    }
                }

                $assignedPayload = [
                    'InventoryDetailID' => $detail->InventoryDetailID,
                    'EndUserID' => (int) $resolvedEndUser->EndUserID,
                    'DepartmentID' => $departmentId,
                    'PropertyCardNumber' => $issuance->DocumentNumber,
                    'Description' => $detail->Description ?? $itemDescription,
                    'AssignedDate' => $issuedAt,
                    'PropertyStatusID' => $shouldFinalize
                        ? 2
                        : (int) ($assignedProperty?->PropertyStatusID ?? 2),
                    'ConditionID' => (int) ($detail->ConditionID ?? 1),
                    'Remarks' => $issuance->Remarks,
                ];

                if ($assignedProperty) {
                    $assignedProperty->fill($assignedPayload);
                    $assignedProperty->save();
                } else {
                    $assignedProperty = AssignedProperty::create($assignedPayload);
                }

                $this->ensureQrCodeForDetail($detail, $assignedProperty);
            }

            $issuances[] = $issuance;
        }

        return $issuances;
    }

    private function ensureInventoryForDeliveryReceipt(DeliveryReceipt $deliveryReceipt): void
    {
        if (PropertyInventory::where('DRID', (int) $deliveryReceipt->DRID)->whereNull('deleted_at')->exists()) {
            return;
        }

        $deliveryReceipt->loadMissing(['details', 'requestedByUser']);
        $details = $deliveryReceipt->details;
        if ($details->isEmpty()) {
            return;
        }

        $resolvedEndUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);

        $inventory = PropertyInventory::create([
            'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $deliveryReceipt->DRID,
            'DRID' => (int) $deliveryReceipt->DRID,
            'IARID' => null,
            'DateReceived' => $deliveryReceipt->DateInspected ?? $deliveryReceipt->DateReceived ?? now(),
            'Remarks' => trim((string) ($deliveryReceipt->Remarks ?? '')) !== ''
                ? trim((string) $deliveryReceipt->Remarks)
                : 'Auto-ingested from DR #' . $deliveryReceipt->DRNumber,
            'CreatedDate' => now(),
        ]);

        $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'To be issued')
            ->orWhere('StatusName', 'For issuance')
            ->orWhere('StatusName', 'For Issuance')
            ->value('PropertyStatusID');

        if (! $toBeIssuedStatusId) {
            $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'Available')
                ->value('PropertyStatusID') ?? 1;
        }

        foreach ($details as $detail) {
            $deliveredQty = (int) ($detail->QuantityDelivered ?? 0);
            if ($deliveredQty <= 0) {
                continue;
            }

            $propertyNumbers = $this->reservePropertyNumbers($deliveredQty);

            foreach ($propertyNumbers as $propertyNumber) {

                $inventoryDetail = PropertyInventoryDetail::create([
                    'InventoryID' => $inventory->InventoryID,
                    'PropertyTypeID' => $detail->PropertyTypeID,
                    'PropertyNumber' => $propertyNumber,
                    'ConditionID' => 1,
                    'Brand' => $detail->Brand,
                    'ModelNumber' => $detail->ModelNumber,
                    'SerialNumber' => $detail->SerialNumber,
                    'Description' => $detail->ItemDescription,
                    'Quantity' => 1,
                    'QuantityAvailable' => 1,
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'UnitOfMeasureID' => $detail->UnitOfMeasureID,
                    'UnitCost' => $detail->UnitCost,
                    'TotalCost' => $detail->UnitCost,
                    'SourceIARDetailID' => null,
                    'SourceDRDetailID' => $detail->DRDetailID,
                    'Remarks' => $detail->ChangeReason,
                    'CreatedDate' => now(),
                ]);

                $assignedPayload = [
                    'InventoryDetailID' => $inventoryDetail->InventoryDetailID,
                    'EndUserID' => (int) $resolvedEndUser->EndUserID,
                    'DepartmentID' => (int) ($resolvedEndUser->DepartmentID ?? 0),
                    'PropertyCardNumber' => null,
                    'Description' => $detail->ItemDescription,
                    'AssignedDate' => now()->toDateString(),
                    'PropertyStatusID' => (int) $toBeIssuedStatusId,
                    'ConditionID' => 1,
                    'Remarks' => 'Auto-created on DR intake',
                ];

                $assignedProperty = AssignedProperty::where('InventoryDetailID', $inventoryDetail->InventoryDetailID)->first();
                if ($assignedProperty) {
                    $assignedProperty->fill($assignedPayload);
                    $assignedProperty->save();
                } else {
                    $assignedProperty = AssignedProperty::create($assignedPayload);
                }

                $this->ensureQrCodeForDetail($inventoryDetail, $assignedProperty);
            }
        }
    }

   private function resolveIssuanceItemName(string $documentType, ?DeliveryReceiptDetail $drDetail, ?string $fallback): string
    {
        $articleName = trim((string) ($drDetail?->article?->ArticleName ?? ''));
        if ($articleName !== '') {
            return $articleName;
        }

        $drItemName = trim((string) ($drDetail?->ItemDescription ?? ''));
        if ($drItemName !== '') {
            return $drItemName;
        }

        $fallbackName = trim((string) ($fallback ?? ''));
        if ($fallbackName !== '') {
            return $fallbackName;
        }

        return 'Item';
    }

}