<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\PropertyReceived;
use App\Models\Procurement\DeliveryReceiptDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\AssignedProperty;
use App\Models\QrCode;

class InventoryController extends Controller
{
    // POST /api/admin/inventory/ingest-from-iar
    public function ingestFromIar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'iar_id' => 'required|integer|exists:inspectionacceptancereport,IARID',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $iarId = $request->input('iar_id');

        // Fetch IAR and its details
        $iar = InspectionAcceptanceReport::with(['details'])->find($iarId);
        if (!$iar) {
            return response()->json(['message' => 'IAR not found.'], 404);
        }
        if ($iar->details->isEmpty()) {
            return response()->json(['message' => 'No IAR details to ingest.'], 422);
        }

        // Start transaction for safety
        DB::beginTransaction();
        try {
            // Create PropertyInventory record
            $inventory = PropertyInventory::create([
                'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $iar->IARID,
                'IARID' => $iar->IARID,
                'DateReceived' => $iar->DateAccepted ?? now(),
                'Remarks' => 'Auto-ingested from IAR #' . $iar->IARNumber,
                'CreatedDate' => now(),
            ]);

            // Initialize Property Number Sequence
            $year = date('Y');
            $prefix = $year . '-';
            // Find the highest existing number for this year
            $latestItem = PropertyInventoryDetail::where('PropertyNumber', 'like', $prefix . '%')
                ->orderBy('PropertyNumber', 'desc')
                ->withTrashed()
                ->first();
            
            $currentSequence = 0;
            if ($latestItem && preg_match('/-(\d+)$/', $latestItem->PropertyNumber, $matches)) {
                $currentSequence = intval($matches[1]);
            }

            // For each IAR detail, create individual PropertyInventoryDetail records
            foreach ($iar->details as $detail) {
                // Get DR detail for cost/unit info
                $drDetail = DeliveryReceiptDetail::find($detail->DRDetailID);
                if (!$drDetail) continue;

                // Determine Brand, Model, Serial (Prefer Verified from IAR, fallback to DR)
                $brand = $detail->VerifiedBrand ?? $drDetail->Brand;
                $model = $detail->VerifiedModel ?? $drDetail->ModelNumber;
                $serial = $detail->VerifiedSerial ?? $drDetail->SerialNumber;

                // Create individual records for each accepted quantity
                for ($i = 0; $i < $detail->QuantityAccepted; $i++) {
                    // For serial numbers, if we have multiple items but only one serial,
                    // we'll use the same serial for all (common for bulk items)
                    // In a real system, you'd want separate serials for each item
                    $itemSerial = $serial;

                    // Assign Property Number
                    $currentSequence++;
                    $propertyNumber = $prefix . str_pad($currentSequence, 6, '0', STR_PAD_LEFT);

                    PropertyInventoryDetail::create([
                        'InventoryID' => $inventory->InventoryID,
                        'PropertyTypeID' => $detail->PropertyTypeID,
                        'PropertyNumber' => $propertyNumber,
                        'ConditionID' => $detail->ConditionID,
                        'Brand' => $brand,
                        'ModelNumber' => $model,
                        'SerialNumber' => $itemSerial,
                        'Description' => $detail->Remarks,
                        'Quantity' => 1, // Each record represents one physical item
                        'QuantityAvailable' => 1,
                        'PropertyStatusID' => 1, // Available
                        'UnitOfMeasureID' => $drDetail->UnitOfMeasureID,
                        'UnitCost' => $drDetail->UnitCost,
                        'TotalCost' => $drDetail->UnitCost, // Cost per individual item
                        'SourceIARDetailID' => $detail->IARDetailID,
                        'SourceDRDetailID' => $detail->DRDetailID,
                        'Remarks' => $detail->Remarks,
                        'CreatedDate' => now(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Inventory ingestion successful.','inventory_id' => $inventory->InventoryID], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ingestion failed: ' . $e->getMessage()], 500);
        }
    }

    // GET /api/admin/inventory/pending-ingestion
    public function pendingIngestion()
    {
        $pendingIars = InspectionAcceptanceReport::whereNotNull('DateAccepted')
            ->whereNotIn('IARID', function($query) {
                $query->select('IARID')->from('propertyinventory')->whereNotNull('IARID');
            })
            ->with([
                'details.drDetail' => function($query) {
                    $query->select('DRDetailID', 'ItemDescription', 'UnitCost', 'Brand', 'ModelNumber', 'SerialNumber');
                },
                'details.propertyType' => function($query) {
                    $query->select('PropertyTypeID', 'PropertyName');
                },
                'signatures' => function($query) {
                    $query->select('id', 'iar_id', 'role', 'url', 'created_at', 'signed_by_user_id')
                          ->with(['user' => function($q) {
                              $q->select('UserID', 'FullName');
                          }]);
                }
            ])
            ->get();

        // Transform the data to match frontend expectations
        $transformedIars = $pendingIars->map(function($iar) {
            return [
                'IARID' => $iar->IARID,
                'IARNumber' => $iar->IARNumber,
                'DateAccepted' => $iar->DateAccepted,
                'details' => $iar->details->map(function($detail) {
                    return [
                        'IARDetailID' => $detail->IARDetailID,
                        'QuantityAccepted' => $detail->QuantityAccepted,
                        'ItemDescription' => $detail->drDetail?->ItemDescription ?? 'No description available',
                        'VerifiedBrand' => $detail->VerifiedBrand,
                        'VerifiedModel' => $detail->VerifiedModel,
                        'VerifiedSerial' => $detail->VerifiedSerial,
                        'UnitCost' => $detail->drDetail?->UnitCost ? (float) $detail->drDetail->UnitCost : 0.0,
                        'propertyType' => $detail->propertyType ? [
                            'PropertyTypeID' => $detail->propertyType->PropertyTypeID,
                            'PropertyName' => $detail->propertyType->PropertyName
                        ] : null
                    ];
                }),
                'signatures' => $iar->signatures->map(function($signature) {
                    return [
                        'id' => $signature->id,
                        'role' => $signature->role,
                        'url' => $signature->url,
                        'created_at' => $signature->created_at,
                        'user' => $signature->user ? [
                            'UserID' => $signature->user->UserID,
                            'FullName' => $signature->user->FullName
                        ] : null
                    ];
                })
            ];
        });

        return response()->json($transformedIars);
    }

    // GET /api/admin/inventory
    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(10, (int) $request->query('per_page', 10));
        $search = $request->query('search');

        $activeIssuanceStatuses = ['pending_acknowledgment', 'acknowledged'];

                $custodianSql = "(
                        SELECT COALESCE(
                                CASE
                                        WHEN pi.DocumentType = 'ICS' THEN reqU.FullName
                                        ELSE eu.FullName
                                END,
                                u.FullName,
                                pi.IssuedToName,
                                reqU.FullName
                        )
                        FROM propertyissuancedetail pidet
                        JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                        JOIN propertyinventorydetail invd ON invd.InventoryDetailID = pidet.InventoryDetailID
                        LEFT JOIN assetrequest ar ON ar.RequestID = pi.RequestID
                        LEFT JOIN user reqU ON reqU.UserID = ar.RequestedBy
                        LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
                        LEFT JOIN user u ON u.UserID = pi.IssuedTo
                        WHERE invd.InventoryID = propertyinventory.InventoryID
                            AND pi.deleted_at IS NULL
                            AND pidet.deleted_at IS NULL
                            AND pi.Status IN ('pending_acknowledgment','acknowledged')
                        ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                        LIMIT 1
                )";

                $locationSql = "(
                        SELECT COALESCE(dep.DepartmentName, depU.DepartmentName, depReq.DepartmentName)
            FROM propertyissuancedetail pidet
            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
            JOIN propertyinventorydetail invd ON invd.InventoryDetailID = pidet.InventoryDetailID
            LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
            LEFT JOIN department dep ON dep.DepartmentID = eu.DepartmentID
                        LEFT JOIN user u ON u.UserID = pi.IssuedTo
                        LEFT JOIN department depU ON depU.DepartmentID = u.DepartmentID
            LEFT JOIN assetrequest ar ON ar.RequestID = pi.RequestID
            LEFT JOIN department depReq ON depReq.DepartmentID = ar.DepartmentID
            WHERE invd.InventoryID = propertyinventory.InventoryID
              AND pi.deleted_at IS NULL
              AND pidet.deleted_at IS NULL
              AND pi.Status IN ('pending_acknowledgment','acknowledged')
            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
            LIMIT 1
        )";

        $lastIssuedDateSql = "(
            SELECT pi.DateIssued
            FROM propertyissuancedetail pidet
            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
            JOIN propertyinventorydetail invd ON invd.InventoryDetailID = pidet.InventoryDetailID
            WHERE invd.InventoryID = propertyinventory.InventoryID
              AND pi.deleted_at IS NULL
              AND pidet.deleted_at IS NULL
              AND pi.Status IN ('pending_acknowledgment','acknowledged')
            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
            LIMIT 1
        )";

        $query = PropertyInventory::query()
            ->select('propertyinventory.*')
            ->addSelect(DB::raw($custodianSql . ' as CurrentCustodianName'))
            ->addSelect(DB::raw($locationSql . ' as CurrentLocation'))
            ->addSelect(DB::raw($lastIssuedDateSql . ' as LastIssuedDate'))
            ->with([
            'details' => function($query) {
                $query->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
                      ->select('propertyinventorydetail.*', 'deliveryreceiptdetail.ItemDescription as dr_item_description');
            },
            'details.propertyType',
            'iar.deliveryReceipt' // Load IAR and its DR
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('InventoryNumber', 'like', '%' . $search . '%')
                  ->orWhere('Remarks', 'like', '%' . $search . '%')
                  ->orWhereHas('iar', function ($iarQuery) use ($search) {
                      $iarQuery->where('IARNumber', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('iar.deliveryReceipt', function ($drQuery) use ($search) {
                      $drQuery->where('DRNumber', 'like', '%' . $search . '%');
                  });
            });
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform data to include description and computed totals
        $paginator->getCollection()->transform(function ($inventory) {
            $firstDetail = $inventory->details->first();
            $description = $firstDetail ? ($firstDetail->Description ?: $firstDetail->dr_item_description ?: 'No description') : 'No description';
            $inventory->Description = $description;
            
            // Compute totals from individual detail records
            $inventory->TotalItems = $inventory->details->sum('Quantity');
            $inventory->TotalValue = $inventory->details->sum('TotalCost');

            // Compute overall status: if all items are assigned, record is Assigned; otherwise Available.
            $totalDetails = $inventory->details->count();
            $assignedDetails = $inventory->details->where('PropertyStatusID', 2)->count();
            $inventory->PrimaryStatus = ($totalDetails > 0 && $assignedDetails === $totalDetails) ? 'Assigned' : 'Available';

            // Provide clean defaults when not currently issued
            $inventory->CurrentCustodianName = $inventory->CurrentCustodianName ?: 'GSO Property Custodian';
            $inventory->CurrentLocation = $inventory->CurrentLocation ?: 'GSO Warehouse';
            
            return $inventory;
        });

        // Return standard Laravel pagination response
        return $paginator;
    }

    // GET /api/admin/inventory/{id}
    public function show($id)
    {
        $activeIssuanceStatuses = ['pending_acknowledgment', 'acknowledged'];

        $inventory = PropertyInventory::with([
            'details' => function($query) {
                $query->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
                      ->leftJoin('propertytype', 'propertyinventorydetail.PropertyTypeID', '=', 'propertytype.PropertyTypeID')
                      ->leftJoin('propertyclassification', 'propertytype.PropertyClassificationID', '=', 'propertyclassification.PropertyClassificationID')
                      ->select(
                              'propertyinventorydetail.*',
                              'deliveryreceiptdetail.ItemDescription as dr_item_description',
                              'propertytype.PropertyName', 'propertytype.Description as property_type_description',
                              'propertyclassification.ClassificationName', 'propertyclassification.DocumentType'
                      )
                                            ->addSelect(DB::raw("(
                                                    SELECT COALESCE(
                                                        CASE
                                                            WHEN pi.DocumentType = 'ICS' THEN reqU.FullName
                                                            ELSE eu.FullName
                                                        END,
                                                        u.FullName,
                                                        pi.IssuedToName,
                                                        reqU.FullName
                                                    )
                                                    FROM propertyissuancedetail pidet
                                                    JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                                    LEFT JOIN assetrequest ar ON ar.RequestID = pi.RequestID
                                                    LEFT JOIN user reqU ON reqU.UserID = ar.RequestedBy
                                                    LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
                                                    LEFT JOIN user u ON u.UserID = pi.IssuedTo
                                                    WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        AND pi.deleted_at IS NULL
                                                        AND pidet.deleted_at IS NULL
                                                        AND pi.Status IN ('pending_acknowledgment','acknowledged')
                                                    ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                                    LIMIT 1
                                            ) as custodianName"))
                                            ->addSelect(DB::raw("(
                                                    SELECT COALESCE(dep.DepartmentName, depU.DepartmentName, depReq.DepartmentName)
                                                    FROM propertyissuancedetail pidet
                                                    JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                                    LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
                                                    LEFT JOIN department dep ON dep.DepartmentID = eu.DepartmentID
                                                    LEFT JOIN user u ON u.UserID = pi.IssuedTo
                                                    LEFT JOIN department depU ON depU.DepartmentID = u.DepartmentID
                                                    LEFT JOIN assetrequest ar ON ar.RequestID = pi.RequestID
                                                    LEFT JOIN department depReq ON depReq.DepartmentID = ar.DepartmentID
                                                    WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        AND pi.deleted_at IS NULL
                                                        AND pidet.deleted_at IS NULL
                                                        AND pi.Status IN ('pending_acknowledgment','acknowledged')
                                                    ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                                    LIMIT 1
                                            ) as locationDepartment"))
                                            ->addSelect(DB::raw("(
                                                    SELECT pi.DateIssued
                                                    FROM propertyissuancedetail pidet
                                                    JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                                    WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        AND pi.deleted_at IS NULL
                                                        AND pidet.deleted_at IS NULL
                                                        AND pi.Status IN ('pending_acknowledgment','acknowledged')
                                                    ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                                    LIMIT 1
                                            ) as lastIssuedDate"));
            },
            'details.qrCode',
            'details.drDetail' => function($query) {
                $query->select('DRDetailID', 'ItemDescription');
            },
            'iar.deliveryReceipt' // Include IAR and DR info
        ])->find($id);
        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        // Map acquisition date name expected by frontend
        if (!isset($inventory->DateAcquired)) {
            $inventory->DateAcquired = $inventory->DateReceived ?? $inventory->CreatedDate ?? null;
        }

        // Compute overall status for the record
        $totalDetails = $inventory->details?->count() ?? 0;
        $assignedDetails = $inventory->details?->where('PropertyStatusID', 2)->count() ?? 0;
        $inventory->PrimaryStatus = ($totalDetails > 0 && $assignedDetails === $totalDetails) ? 'Assigned' : 'Available';

        return response()->json($inventory);
    }

    // POST /api/admin/inventory/details/{id}/generate-qr
    public function generateQr(Request $request, $id)
    {
        $detail = PropertyInventoryDetail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Inventory detail not found.'], 404);
        }

        $assignedProperty = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->first();
        if (!$assignedProperty) {
            return response()->json([
                'message' => 'Cannot generate QR: item has no assigned property record yet. Issue the item first to create accountability (assignedproperty), then generate QR.',
            ], 422);
        }

        $force = $request->boolean('force', false);

        // If already has QR and not forcing, return existing
        if ($detail->qrCode && !$force) {
            return response()->json(['message' => 'QR already exists', 'qr' => $detail->qrCode], 200);
        }

        $baseUrl = rtrim((string) config('gso.frontend_url'), '/');
        if ($baseUrl === '') {
            $baseUrl = 'http://localhost:3000';
        }

        $hash = substr(hash_hmac('sha256', (string) $assignedProperty->PropertyID, (string) config('app.key')), 0, 12);
        $payload = $baseUrl . '/qr/scan/' . $assignedProperty->PropertyID . '?h=' . $hash;

        $qr = QrCode::updateOrCreate(
            ['InventoryDetailID' => $detail->InventoryDetailID],
            [
                'PropertyID' => (int) $assignedProperty->PropertyID,
                'DepartmentID' => (int) $assignedProperty->DepartmentID,
                'QRCodeData' => $payload,
                'DateGenerated' => now(),
            ]
        );

        return response()->json(['message' => 'QR generated', 'qr' => $qr], 201);
    }

    // PUT /api/admin/inventory/details/{id}
    public function updateDetail(Request $request, $id)
    {
        $detail = PropertyInventoryDetail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Inventory detail not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $detail->update($request->only(['Description']));
        $detail->UpdatedDate = now();
        $detail->save();

        return response()->json(['message' => 'Inventory detail updated successfully', 'detail' => $detail]);
    }

    // GET /api/admin/inventory/all-items
    public function allItems(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(10, (int) $request->query('per_page', 10));
        $search = $request->query('search');
        $statusId = $request->query('property_status_id');
        $propertyTypeId = $request->query('property_type_id');

        $query = PropertyInventoryDetail::leftJoin('propertytype', 'propertyinventorydetail.PropertyTypeID', '=', 'propertytype.PropertyTypeID')
            ->leftJoin('propertyclassification', 'propertytype.PropertyClassificationID', '=', 'propertyclassification.PropertyClassificationID')
            ->leftJoin('propertyinventory', 'propertyinventorydetail.InventoryID', '=', 'propertyinventory.InventoryID')
            ->leftJoin('propertystatus', 'propertyinventorydetail.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
            ->leftJoin('condition', 'propertyinventorydetail.ConditionID', '=', 'condition.ConditionID')
            ->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
            ->select(
                'propertyinventorydetail.*',
                'propertytype.PropertyName',
                'propertyclassification.ClassificationName',
                'propertyclassification.DocumentType',
                'propertyinventory.InventoryNumber',
                'propertystatus.StatusName',
                'condition.ConditionName',
                'deliveryreceiptdetail.ItemDescription as dr_item_description',
                'deliveryreceiptdetail.Brand as dr_brand',
                'deliveryreceiptdetail.ModelNumber as dr_model',
                'deliveryreceiptdetail.SerialNumber as dr_serial'
            )
            ->addSelect([
                                'qrCodeData' => DB::raw("(
                                        SELECT q.QRCodeData
                                        FROM qrcode q
                                        WHERE q.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                        LIMIT 1
                                ) as qrCodeData"),
                                'custodianName' => DB::raw("(
                                        SELECT COALESCE(
                                            CASE
                                                WHEN pi.DocumentType = 'ICS' THEN reqU.FullName
                                                ELSE eu.FullName
                                            END,
                                            u.FullName,
                                            pi.IssuedToName,
                                            reqU.FullName
                                        )
                                        FROM propertyissuancedetail pidet
                                        JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                        LEFT JOIN assetrequest ar ON ar.RequestID = pi.RequestID
                                        LEFT JOIN user reqU ON reqU.UserID = ar.RequestedBy
                                        LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
                                        LEFT JOIN user u ON u.UserID = pi.IssuedTo
                                        WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            AND pi.deleted_at IS NULL
                                            AND pidet.deleted_at IS NULL
                                            AND pi.Status IN ('pending_acknowledgment','acknowledged')
                                        ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                        LIMIT 1
                                ) as custodianName"),
                                'locationDepartment' => DB::raw("(
                                        SELECT COALESCE(dep.DepartmentName, depU.DepartmentName, depReq.DepartmentName)
                                        FROM propertyissuancedetail pidet
                                        JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                        LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
                                        LEFT JOIN department dep ON dep.DepartmentID = eu.DepartmentID
                                        LEFT JOIN user u ON u.UserID = pi.IssuedTo
                                        LEFT JOIN department depU ON depU.DepartmentID = u.DepartmentID
                                        LEFT JOIN assetrequest ar ON ar.RequestID = pi.RequestID
                                        LEFT JOIN department depReq ON depReq.DepartmentID = ar.DepartmentID
                                        WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            AND pi.deleted_at IS NULL
                                            AND pidet.deleted_at IS NULL
                                            AND pi.Status IN ('pending_acknowledgment','acknowledged')
                                        ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                        LIMIT 1
                                ) as locationDepartment"),
                                'lastIssuedDate' => DB::raw("(
                                        SELECT pi.DateIssued
                                        FROM propertyissuancedetail pidet
                                        JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                        WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            AND pi.deleted_at IS NULL
                                            AND pidet.deleted_at IS NULL
                                            AND pi.Status IN ('pending_acknowledgment','acknowledged')
                                        ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                        LIMIT 1
                                ) as lastIssuedDate"),
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('propertyinventorydetail.Brand', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.ModelNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.SerialNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.PropertyNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventory.InventoryNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertytype.PropertyName', 'like', '%' . $search . '%')
                  ->orWhere('deliveryreceiptdetail.ItemDescription', 'like', '%' . $search . '%');
            });
        }

        if ($statusId) {
            $query->where('propertyinventorydetail.PropertyStatusID', $statusId);
        }

        if ($propertyTypeId) {
            $query->where('propertyinventorydetail.PropertyTypeID', $propertyTypeId);
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return $paginator;
    }
}
