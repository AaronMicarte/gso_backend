<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\AssetAuditLog;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\EndUser;
use App\Services\AssetRequestSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyIssuanceController extends Controller
{
    /**
     * Get all property issuances
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PropertyIssuance::with([
            'request.requester.department:DepartmentID,DepartmentName',
            'request.department:DepartmentID,DepartmentName',
            'issuedBy',
            'issuedTo',
            'details.inventoryDetail:InventoryDetailID,Description,Brand,ModelNumber,PropertyNumber,SerialNumber,UnitCost',
            'details.inventoryDetail.inventory',
            'details.inventoryDetail.unitOfMeasure',
        ]);

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

        // Status filter (based on request status)
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                $query->whereNotNull('DateIssued');
            }
        }

        $issuances = $query->orderBy('DateIssued', 'desc')
            ->paginate($request->get('per_page', 15));

        // Transform data to group by request
        $requestGroups = $issuances->getCollection()->groupBy('RequestID');
        $transformedData = [];

        foreach ($requestGroups as $requestId => $requestIssuances) {
            $firstIssuance = $requestIssuances->first();
            $request = $firstIssuance->request;

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
                unset($issuance->issuedTo, $issuance->issuedBy, $issuance->DateIssued);
                return $issuance;
            });

            $transformedData[] = [
                'RequestID' => $requestId,
                'RequestNumber' => $request->RequestNumber ?? 'N/A',
                'RequestDate' => $request->RequestDate ?? null,
                'requester' => $request->requester ? [
                    'FullName' => $request->requester->FullName ?? 'Unknown User',
                    'Department' => $request->requester->department->DepartmentName ?? 'Unknown Department'
                ] : [
                    'FullName' => 'Unknown User',
                    'Department' => 'Unknown Department'
                ],
                'department' => $request->department ? [
                    'DepartmentName' => $request->department->DepartmentName ?? 'Unknown Department'
                ] : [
                    'DepartmentName' => 'Unknown Department'
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
            'issuedBy',
            'issuedTo',
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
        ])->findOrFail($id);

        // Transform the issuance data to match frontend expectations
        $transformedIssuance = [
            'IssuanceID' => $issuance->IssuanceID,
            'RequestID' => $issuance->RequestID,
            'DocumentType' => $issuance->DocumentType,
            'DocumentNumber' => $issuance->DocumentNumber,
            'IssuedTo' => $issuance->IssuedTo,
            'IssuedToName' => $issuance->IssuedToName,
            'IssuedBy' => $issuance->IssuedBy,
            'IssuanceDate' => $issuance->DateIssued,
            'Status' => $issuance->Status,
            'Remarks' => $issuance->Remarks,
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
        $transformedIssuance['issuer'] = $issuance->issuedBy;

        // Handle issuedTo relationship
        // For ICS, the recipient should be the request's requester (dept head).
        if ($issuance->DocumentType === 'ICS' && $issuance->request && $issuance->request->requester) {
            $transformedIssuance['issuedToUser'] = $issuance->request->requester;
            $transformedIssuance['IssuedToName'] = $issuance->request->requester->FullName ?? $transformedIssuance['IssuedToName'];
        } elseif ($issuance->issuedTo) {
            $transformedIssuance['issuedToUser'] = $issuance->issuedTo;
        } else {
            $transformedIssuance['issuedToUser'] = [
                'FullName' => $issuance->IssuedToName ?? 'External Recipient',
                'department' => null
            ];
        }

        // Transform details
        $transformedIssuance['details'] = $issuance->details->map(function ($detail) {
            $inventoryDetail = $detail->inventoryDetail;

            // Item Name should come from DR (deliveryreceiptdetail.ItemDescription) when available.
            // Fallback to issuance detail ItemDescription (legacy/manual).
            $itemName = $inventoryDetail?->drDetail?->ItemDescription
                ?? $detail->ItemDescription
                ?? 'Item';

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

        return response()->json($transformedIssuance);
    }

    /**
     * Create new property issuance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:assetrequest,RequestID',
            'date_issued' => 'required|date',
            'remarks' => 'nullable|string',
            'issuerSignature' => 'required|string', // base64 data URL
            'issuerSignatureSource' => 'required|in:canvas,upload',
            'inventory_items' => 'required|array',
            'inventory_items.*.request_detail_id' => 'required|integer',
            'inventory_items.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'inventory_items.*.quantity_issued' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

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

            // Verify the request is approved
            if ($assetRequest->Status !== 'approved') {
                return response()->json([
                    'message' => 'Only approved requests can be issued',
                    'error' => 'Invalid request status'
                ], 422);
            }

            // Group items by recipient and classification
            // - PAR: group by assigned end user (ID or Name)
            // - ICS: always group to the request requester (dept head)
            $groupedItems = $assetRequest->details->groupBy(function ($detail) use ($assetRequest) {
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

                // Generate document number
                $documentNumber = $this->generateDocumentNumber($classification);

                // Prepare issuance data
                $issuanceData = [
                    'RequestID' => $validated['request_id'],
                    'IssuedBy' => Auth::id(),
                    'DocumentType' => $classification,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $validated['date_issued'],
                    'Remarks' => $validated['remarks'] ?? null,
                    'Status' => 'pending_acknowledgment',
                    'CreatedDate' => now(),
                ];

                if ($classification === 'ICS') {
                    // ICS recipient must be the requester (dept head)
                    $issuanceData['IssuedTo'] = $assetRequest->RequestedBy;
                    if (!$issuanceData['IssuedTo']) {
                        $issuanceData['IssuedToName'] = $assetRequest->requester->FullName
                            ?? $endUserName
                            ?? 'Unspecified User';
                    }
                } elseif ($endUserId) {
                    $issuanceData['IssuedTo'] = $endUserId;
                } else {
                    $issuanceData['IssuedTo'] = null;
                    $issuanceData['IssuedToName'] = $endUserName ?? 'Unspecified User';
                }

                // Create the property issuance
                $issuance = PropertyIssuance::create($issuanceData);

                // Create issuance details from asset request details
                foreach ($items as $detail) {
                    $detailHasInventory = false;
                    
                    if (isset($inventoryMap[$detail->RequestDetailID])) {
                        // Create one issuance detail per assigned inventory item
                        foreach ($inventoryMap[$detail->RequestDetailID] as $inventoryItem) {
                            $qtyIssued = $inventoryItem['quantity_issued'];

                            PropertyIssuanceDetail::create([
                                'IssuanceID' => $issuance->IssuanceID,
                                'RequestDetailID' => $detail->RequestDetailID,
                                'InventoryDetailID' => $inventoryItem['inventory_detail_id'],
                                'ItemDescription' => $detail->ItemDescription,
                                'Unit' => $detail->Unit,
                                'QuantityIssued' => $qtyIssued, // 1 per item usually
                                'Remarks' => $detail->AdminNotes,
                            ]);
                            $detailHasInventory = true;

                            // Update Inventory Status
                            $invDetail = PropertyInventoryDetail::find($inventoryItem['inventory_detail_id']);
                            if ($invDetail) {
                                // Decrease available quantity
                                $invDetail->decrement('QuantityAvailable', $qtyIssued);
                                
                                // If no more available quantity, mark as Assigned
                                if ($invDetail->fresh()->QuantityAvailable <= 0) {
                                     $invDetail->update(['PropertyStatusID' => 2]); // 2 = Assigned
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
            $signatureService = new AssetRequestSignatureService();
            $signatureService->processAndStore(
                $validated['request_id'],
                'issuer',
                $validated['issuerSignature'],
                $validated['issuerSignatureSource'],
                Auth::id()
            );

            DB::commit();

            return response()->json([
                'message' => 'Assets issued successfully',
                'data' => [
                    'issuances' => collect($issuances)->map(function ($issuance) {
                        return $issuance->load([
                            'request',
                            'issuedBy:UserID,FullName',
                            'issuedTo:EndUserID,FullName',
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

    /**
     * Get approved asset requests for issuance
     */
    public function getApprovedRequests(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get approved requests that haven't been issued yet
            $query = AssetRequest::where('Status', 'approved')
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
            
            $query = AssetRequest::where('Status', 'approved')
                ->with([
                    'requester:UserID,FullName',
                    'department:DepartmentID,DepartmentName',
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
     * Get available inventory items for issuance
     */
    public function getAvailableInventory(Request $request)
    {
        try {
            $query = \App\Models\Inventory\PropertyInventoryDetail::leftJoin('propertytype', 'propertyinventorydetail.PropertyTypeID', '=', 'propertytype.PropertyTypeID')
                ->leftJoin('propertyinventory', 'propertyinventorydetail.InventoryID', '=', 'propertyinventory.InventoryID')
                ->leftJoin('propertystatus', 'propertyinventorydetail.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                ->leftJoin('condition', 'propertyinventorydetail.ConditionID', '=', 'condition.ConditionID')
                ->leftJoin('unitofmeasure', 'propertyinventorydetail.UnitOfMeasureID', '=', 'unitofmeasure.UnitOfMeasureID')
                ->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
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
                ->whereDoesntHave('assetAssignments');

            // Only include items that are currently marked as available.
            // (Assigned items may not always have an assetAssignments record depending on legacy data.)
            $query->where('propertyinventorydetail.PropertyStatusID', 1);

            // Filter by search term
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('propertyinventorydetail.Description', 'like', "%{$search}%")
                      ->orWhere('propertyinventorydetail.Brand', 'like', "%{$search}%")
                      ->orWhere('propertyinventorydetail.ModelNumber', 'like', "%{$search}%")
                      ->orWhere('propertyinventorydetail.PropertyNumber', 'like', "%{$search}%")
                      ->orWhere('propertyinventorydetail.SerialNumber', 'like', "%{$search}%")
                      ->orWhere('deliveryreceiptdetail.ItemDescription', 'like', "%{$search}%");
                });
            }

            // Filter by property type
            if ($request->filled('property_type')) {
                $query->where('propertyinventorydetail.PropertyTypeID', $request->property_type);
            }

            $items = $query->orderBy('propertyinventorydetail.PropertyNumber', 'asc')
                ->paginate($request->get('per_page', 50));

            return response()->json($items);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving available inventory: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Acknowledge receipt of issued asset
     */
    public function acknowledge(Request $request, $id)
    {
        // Accept both the canonical payload keys and legacy keys used by older frontend code.
        $validated = $request->validate([
            'acknowledgment_signature' => 'required_without:signature|string',
            'signature' => 'required_without:acknowledgment_signature|string',
            'acknowledgment_notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'acknowledged_by' => 'required|integer|exists:user,UserID',
        ]);

        try {
            DB::beginTransaction();

            $issuance = PropertyIssuance::findOrFail($id);

            $signature = $validated['acknowledgment_signature'] ?? $validated['signature'];
            $notes = $validated['acknowledgment_notes'] ?? $validated['notes'] ?? null;
            
            // Update acknowledgment info
            $issuance->update([
                'AcknowledgedBy' => $validated['acknowledged_by'],
                'AcknowledgedAt' => now(),
                'AcknowledgmentSignature' => $signature,
                'AcknowledgmentNotes' => $notes,
                'Status' => 'acknowledged'
            ]);

            // If all issuances for the request have been acknowledged, mark the request as received.
            if ($issuance->RequestID) {
                $hasPending = PropertyIssuance::where('RequestID', $issuance->RequestID)
                    ->where('Status', '!=', 'acknowledged')
                    ->exists();

                if (!$hasPending) {
                    AssetRequest::where('RequestID', $issuance->RequestID)
                        ->update(['Status' => 'received']);
                }
            }

            // Create audit log
            AssetAuditLog::create([
                'EntityType' => 'property_issuance',
                'EntityID' => $issuance->IssuanceID,
                'Action' => 'acknowledged',
                'PerformedBy' => Auth::id(),
                'Details' => json_encode([
                    'document_number' => $issuance->DocumentNumber,
                    'acknowledged_by' => $validated['acknowledged_by'],
                    'acknowledgment_date' => now()->toISOString()
                ]),
                'IPAddress' => $request->ip(),
                'UserAgent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Issuance acknowledged successfully',
                'data' => $issuance->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to acknowledge issuance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}