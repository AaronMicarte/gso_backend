<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\PropertyIssuanceDetail;
use App\Models\PropertyTransferItem;
use App\Models\AssetAuditLog;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\Procurement\IARDetail;
use App\Models\MasterData\PropertyStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\AssignedProperty;
use App\Models\QrCode;

class InventoryController extends Controller
{
    // POST /api/admin/inventory/ingest-from-iar
    public function ingestFromIar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dr_id' => 'required|integer|exists:deliveryreceipt,DRID',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $drId = (int) $request->input('dr_id');

        if (PropertyInventory::where('DRID', $drId)->exists()) {
            return response()->json([
                'message' => 'Inventory already exists for this delivery receipt.',
            ], 200);
        }

        return response()->json([
            'message' => 'Inventory ingestion is handled during delivery receipt inspection. Re-save the DR if inventory is missing.',
            'error' => 'inventory_not_ready'
        ], 409);
    }

    // GET /api/admin/inventory/pending-ingestion
    public function pendingIngestion()
    {
        $pendingReceipts = DeliveryReceipt::where('InspectionStatus', 'inspected')
            ->whereNotIn('DRID', function ($query) {
                $query->select('DRID')->from('propertyinventory')->whereNotNull('DRID');
            })
            ->with([
                'details.propertyType',
                'details.unitOfMeasure',
            ])
            ->get();

        $transformed = $pendingReceipts->map(function ($dr) {
            return [
                'DRID' => $dr->DRID,
                'DRNumber' => $dr->DRNumber,
                'DateInspected' => $dr->DateInspected,
                'details' => $dr->details->map(function ($detail) {
                    return [
                        'DRDetailID' => $detail->DRDetailID,
                        'QuantityDelivered' => $detail->QuantityDelivered,
                        'ItemDescription' => $detail->ItemDescription ?? 'No description available',
                        'UnitCost' => $detail->UnitCost ? (float) $detail->UnitCost : 0.0,
                        'propertyType' => $detail->propertyType ? [
                            'PropertyTypeID' => $detail->propertyType->PropertyTypeID,
                            'PropertyName' => $detail->propertyType->PropertyName
                        ] : null,
                    ];
                }),
            ];
        });

        return response()->json($transformed);
    }

    // GET /api/admin/inventory
    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(10, (int) $request->query('per_page', 10));
        $search = $request->query('search');

        $custodianSql = "COALESCE(
                (
                    SELECT euAP.FullName
                    FROM propertyinventorydetail invd
                    JOIN assignedproperty ap ON ap.InventoryDetailID = invd.InventoryDetailID
                    LEFT JOIN enduser euAP ON euAP.EndUserID = ap.EndUserID
                    WHERE invd.InventoryID = propertyinventory.InventoryID
                    ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                    LIMIT 1
                ),
                (
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
                        AND pi.Status = 'acknowledged'
                    ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                    LIMIT 1
                ),
                (
                    SELECT COALESCE(drReqU.FullName, 'Unknown Requester')
                    FROM propertyinventorydetail invd
                    JOIN deliveryreceiptdetail drd ON drd.DRDetailID = invd.SourceDRDetailID
                    JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                    LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                    WHERE invd.InventoryID = propertyinventory.InventoryID
                    ORDER BY drd.DRDetailID ASC
                    LIMIT 1
                )
            )";

        $locationSql = "COALESCE(
            (
                SELECT depAP.DepartmentName
                FROM propertyinventorydetail invd
                JOIN assignedproperty ap ON ap.InventoryDetailID = invd.InventoryDetailID
                LEFT JOIN department depAP ON depAP.DepartmentID = ap.DepartmentID
                WHERE invd.InventoryID = propertyinventory.InventoryID
                ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                LIMIT 1
            ),
                (
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
                      AND pi.Status = 'acknowledged'
                    ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                    LIMIT 1
                ),
                (
                    SELECT dep.DepartmentName
                    FROM propertyinventorydetail invd
                    JOIN deliveryreceiptdetail drd ON drd.DRDetailID = invd.SourceDRDetailID
                    JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                    LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                    LEFT JOIN department dep ON dep.DepartmentID = drReqU.DepartmentID
                    WHERE invd.InventoryID = propertyinventory.InventoryID
                    ORDER BY drd.DRDetailID ASC
                    LIMIT 1
                )
            )";

        $locationDepartmentIdSql = "COALESCE(
            (
                SELECT ap.DepartmentID
                FROM propertyinventorydetail invd
                JOIN assignedproperty ap ON ap.InventoryDetailID = invd.InventoryDetailID
                WHERE invd.InventoryID = propertyinventory.InventoryID
                ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                LIMIT 1
            ),
            (
                SELECT COALESCE(depReq.DepartmentID, dep.DepartmentID, depU.DepartmentID)
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
                    AND pi.Status = 'acknowledged'
                ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                LIMIT 1
            ),
            (
                SELECT dep.DepartmentID
                FROM propertyinventorydetail invd
                JOIN deliveryreceiptdetail drd ON drd.DRDetailID = invd.SourceDRDetailID
                JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                LEFT JOIN department dep ON dep.DepartmentID = drReqU.DepartmentID
                WHERE invd.InventoryID = propertyinventory.InventoryID
                ORDER BY drd.DRDetailID ASC
                LIMIT 1
            )
        )";

        $lastIssuedDateSql = "(
            SELECT pi.DateIssued
            FROM propertyissuancedetail pidet
            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
            JOIN propertyinventorydetail invd ON invd.InventoryDetailID = pidet.InventoryDetailID
            WHERE invd.InventoryID = propertyinventory.InventoryID
              AND pi.deleted_at IS NULL
              AND pidet.deleted_at IS NULL
              AND pi.Status = 'acknowledged'
            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
            LIMIT 1
        )";

        $query = PropertyInventory::query()
            ->select('propertyinventory.*')
            ->addSelect(DB::raw($custodianSql . ' as CurrentCustodianName'))
            ->addSelect(DB::raw($locationSql . ' as CurrentLocation'))
            ->addSelect(DB::raw($locationDepartmentIdSql . ' as CurrentLocationDepartmentID'))
            ->addSelect(DB::raw($lastIssuedDateSql . ' as LastIssuedDate'))
            ->with([
                'details' => function($query) {
                    $query->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
                          ->leftJoin('articles', 'deliveryreceiptdetail.ArticleID', '=', 'articles.ArticleID')
                          ->select(
                              'propertyinventorydetail.*',
                              'deliveryreceiptdetail.ItemDescription as dr_item_description',
                              'articles.ArticleName as dr_article_name'
                          );
                },
                'details.propertyType',
                'deliveryReceipt'
            ]);

        $sortBy = (string) $request->query('sort_by', 'date');
        $sortDir = strtolower((string) $request->query('sort_dir', 'desc'));

        $dateSortColumn = 'created_at';
        foreach (['DateAcquired', 'DateReceived', 'CreatedDate', 'created_at'] as $candidate) {
            if (Schema::hasColumn('propertyinventory', $candidate)) {
                $dateSortColumn = $candidate;
                break;
            }
        }

        $validSortColumns = [
            'date' => $dateSortColumn,
            'inventory_number' => 'InventoryNumber',
        ];

        $sortColumn = $validSortColumns[$sortBy] ?? $dateSortColumn;
        $sortDirection = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : 'desc';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('InventoryNumber', 'like', '%' . $search . '%')
                  ->orWhere('Remarks', 'like', '%' . $search . '%')
                  ->orWhereHas('deliveryReceipt', function ($drQuery) use ($search) {
                      $drQuery->where('DRNumber', 'like', '%' . $search . '%');
                  });
            });
        }

        $query->orderBy($sortColumn, $sortDirection);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $departmentIds = $paginator->getCollection()
            ->pluck('CurrentLocationDepartmentID')
            ->filter(fn ($id) => !empty($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $departmentHeadByDepartment = [];
        if ($departmentIds->isNotEmpty()) {
            $headRows = DB::table('user as u')
                ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
                ->where('u.IsActive', 1)
                ->where('ur.RoleName', 'Department Head')
                ->whereIn('u.DepartmentID', $departmentIds->all())
                ->orderBy('u.DepartmentID')
                ->orderByDesc(DB::raw('COALESCE(u.UpdatedDate, u.CreatedDate)'))
                ->orderByDesc('u.UserID')
                ->get(['u.DepartmentID', 'u.FullName']);

            foreach ($headRows as $row) {
                $deptId = (int) ($row->DepartmentID ?? 0);
                if ($deptId > 0 && !isset($departmentHeadByDepartment[$deptId])) {
                    $departmentHeadByDepartment[$deptId] = trim((string) ($row->FullName ?? ''));
                }
            }
        }

        $paginator->getCollection()->transform(function ($inventory) use ($departmentHeadByDepartment) {
            $firstDetail = $inventory->details->first();
            $description = $firstDetail ? ($firstDetail->Description ?: $firstDetail->dr_item_description ?: 'No description') : 'No description';
            $inventory->Description = $description;

            $inventory->TotalItems = $inventory->details->sum('Quantity');
            $inventory->TotalValue = $inventory->details->sum('TotalCost');

            $totalDetails = $inventory->details->count();
            $assignedDetails = $inventory->details->where('PropertyStatusID', 2)->count();
            $inventory->PrimaryStatus = ($totalDetails > 0 && $assignedDetails === $totalDetails) ? 'Assigned' : 'For issuance';

            $departmentId = (int) ($inventory->CurrentLocationDepartmentID ?? 0);
            $headName = $departmentId > 0 ? ($departmentHeadByDepartment[$departmentId] ?? null) : null;
            $hasHead = is_string($headName) && trim($headName) !== '';
            $inventory->CurrentDepartmentHead = $hasHead ? $headName : null;
            $inventory->DepartmentHeadVacant = $departmentId > 0 && !$hasHead;
            $inventory->AccountabilityLabel = $inventory->DepartmentHeadVacant
                ? 'No current department head'
                : ($inventory->CurrentDepartmentHead ?? null);

            if (!empty($inventory->AccountabilityLabel)) {
                $inventory->CurrentCustodianName = $inventory->AccountabilityLabel;
            } elseif (empty($inventory->CurrentCustodianName)) {
                $inventory->CurrentCustodianName = 'Requester not set';
            }
            $inventory->CurrentLocation = $inventory->CurrentLocation ?: 'Department not set';

            return $inventory;
        });

        return $paginator;
    }

    // GET /api/admin/inventory/{id}
    public function show($id)
    {
        $inventory = PropertyInventory::with([
            'details' => function($query) {
                $query->leftJoin('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
                      ->leftJoin('articles', 'deliveryreceiptdetail.ArticleID', '=', 'articles.ArticleID')
                      ->leftJoin('propertytype', 'propertyinventorydetail.PropertyTypeID', '=', 'propertytype.PropertyTypeID')
                      ->leftJoin('propertyclassification', 'propertytype.PropertyClassificationID', '=', 'propertyclassification.PropertyClassificationID')
                      ->leftJoin('propertystatus', 'propertyinventorydetail.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                      ->leftJoin('condition', 'propertyinventorydetail.ConditionID', '=', 'condition.ConditionID')
                      ->select(
                              'propertyinventorydetail.*',
                              'deliveryreceiptdetail.ItemDescription as dr_item_description',
                          'articles.ArticleName as dr_article_name',
                              'propertytype.PropertyName', 'propertytype.Description as property_type_description',
                              'propertyclassification.ClassificationName', 'propertyclassification.DocumentType',
                              'propertystatus.StatusName',
                              DB::raw("COALESCE(condition.ConditionName,
                                  CASE
                                      WHEN propertyinventorydetail.PropertyStatusID = 2 THEN 'Serviceable'
                                      WHEN propertyinventorydetail.PropertyStatusID = 3 THEN 'Under Repair'
                                      WHEN propertyinventorydetail.PropertyStatusID IN (4,5) THEN 'Unserviceable'
                                      ELSE 'Unknown'
                                  END
                              ) as ConditionName")
                      )
                                            ->addSelect(DB::raw("COALESCE(
                                                (
                                                        SELECT euAP.FullName
                                                        FROM assignedproperty ap
                                                        LEFT JOIN enduser euAP ON euAP.EndUserID = ap.EndUserID
                                                        WHERE ap.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                                                        LIMIT 1
                                                ),
                        (
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
                                AND pi.Status = 'acknowledged'
                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                            LIMIT 1
                        ),
                        (
                            SELECT COALESCE(drReqU.FullName, 'Unknown Requester')
                            FROM deliveryreceiptdetail drd
                            JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                            LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                            WHERE drd.DRDetailID = propertyinventorydetail.SourceDRDetailID
                            LIMIT 1
                        )
                      ) as custodianName"))
                                            ->addSelect(DB::raw("COALESCE(
                                                (
                                                        SELECT depAP.DepartmentName
                                                        FROM assignedproperty ap
                                                        LEFT JOIN department depAP ON depAP.DepartmentID = ap.DepartmentID
                                                        WHERE ap.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                                                        LIMIT 1
                                                ),
                        (
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
                                AND pi.Status = 'acknowledged'
                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                            LIMIT 1
                        ),
                        (
                            SELECT depDR.DepartmentName
                            FROM deliveryreceiptdetail drd
                            JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                            LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                            LEFT JOIN department depDR ON depDR.DepartmentID = drReqU.DepartmentID
                            WHERE drd.DRDetailID = propertyinventorydetail.SourceDRDetailID
                            LIMIT 1
                        )
                      ) as locationDepartment"))
                                            ->addSelect(DB::raw("COALESCE(
                                                (
                                                        SELECT ap.DepartmentID
                                                        FROM assignedproperty ap
                                                        WHERE ap.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                                                        LIMIT 1
                                                ),
                        (
                            SELECT COALESCE(depReq.DepartmentID, dep.DepartmentID, depU.DepartmentID)
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
                                AND pi.Status = 'acknowledged'
                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                            LIMIT 1
                        ),
                        (
                            SELECT depDR.DepartmentID
                            FROM deliveryreceiptdetail drd
                            JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                            LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                            LEFT JOIN department depDR ON depDR.DepartmentID = drReqU.DepartmentID
                            WHERE drd.DRDetailID = propertyinventorydetail.SourceDRDetailID
                            LIMIT 1
                        )
                      ) as locationDepartmentId"))
                                            ->addSelect(DB::raw("(
                                                SELECT COALESCE(beu.FullName, bu.FullName)
                                                FROM propertyborrowitems pbi
                                                JOIN propertyborrow pb ON pb.BorrowID = pbi.BorrowID
                                                LEFT JOIN enduser beu ON beu.EndUserID = pb.ToEndUserID
                                                LEFT JOIN user bu ON bu.UserID = pb.RequestedByUserID
                                                WHERE pbi.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                                        AND pb.deleted_at IS NULL
                                                        AND pbi.deleted_at IS NULL
                                                        AND pb.Status IN ('borrowed','partially_returned')
                                                        AND NOT EXISTS (
                                                                SELECT 1
                                                                FROM propertyreturnitems pri
                                                                JOIN propertyreturn pr ON pr.ReturnID = pri.ReturnID
                                                                WHERE pri.BorrowItemID = pbi.BorrowItemID
                                                                        AND pri.deleted_at IS NULL
                                                                        AND pr.deleted_at IS NULL
                                                        )
                                                ORDER BY pb.BorrowedAt DESC, pb.BorrowID DESC
                                                LIMIT 1
                                            ) as currentBorrowerName"))
                      ->addSelect(DB::raw("(
                            SELECT pi.DateIssued
                            FROM propertyissuancedetail pidet
                            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                            WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                AND pi.deleted_at IS NULL
                                AND pidet.deleted_at IS NULL
                                AND pi.Status = 'acknowledged'
                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                            LIMIT 1
                      ) as lastIssuedDate"));
            },
            'details.qrCode',
            'details.drDetail' => function($query) {
                $query->select('DRDetailID', 'ItemDescription');
            },
            'iar.deliveryReceipt'
        ])->find($id);
        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        if (!isset($inventory->DateAcquired)) {
            $inventory->DateAcquired = $inventory->DateReceived ?? $inventory->CreatedDate ?? null;
        }

        $totalDetails = $inventory->details?->count() ?? 0;
        $assignedDetails = $inventory->details?->where('PropertyStatusID', 2)->count() ?? 0;
        $inventory->PrimaryStatus = ($totalDetails > 0 && $assignedDetails === $totalDetails) ? 'Assigned' : 'For issuance';

        $detailDepartmentIds = $inventory->details
            ->pluck('locationDepartmentId')
            ->filter(fn ($id) => !empty($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $departmentHeadByDepartment = [];
        if ($detailDepartmentIds->isNotEmpty()) {
            $headRows = DB::table('user as u')
                ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
                ->where('u.IsActive', 1)
                ->where('ur.RoleName', 'Department Head')
                ->whereIn('u.DepartmentID', $detailDepartmentIds->all())
                ->orderBy('u.DepartmentID')
                ->orderByDesc(DB::raw('COALESCE(u.UpdatedDate, u.CreatedDate)'))
                ->orderByDesc('u.UserID')
                ->get(['u.DepartmentID', 'u.FullName']);

            foreach ($headRows as $row) {
                $deptId = (int) ($row->DepartmentID ?? 0);
                if ($deptId > 0 && !isset($departmentHeadByDepartment[$deptId])) {
                    $departmentHeadByDepartment[$deptId] = trim((string) ($row->FullName ?? ''));
                }
            }
        }

        $inventory->details->transform(function ($detail) use ($departmentHeadByDepartment) {
            $departmentId = (int) ($detail->locationDepartmentId ?? 0);
            $headName = $departmentId > 0 ? ($departmentHeadByDepartment[$departmentId] ?? null) : null;
            $hasHead = is_string($headName) && trim($headName) !== '';

            $detail->currentDepartmentHead = $hasHead ? $headName : null;
            $detail->departmentHeadVacant = $departmentId > 0 && !$hasHead;
            $detail->accountabilityLabel = $detail->departmentHeadVacant
                ? 'No current department head'
                : ($detail->currentDepartmentHead ?? null);

            return $detail;
        });

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

        $beforeDescription = $detail->Description;

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

        $afterDescription = $detail->Description;
        if ($beforeDescription !== $afterDescription) {
            $userId = (int) (Auth::id() ?? 0);
            if ($userId > 0) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $detail->InventoryDetailID,
                    'description_updated',
                    $userId,
                    [
                        'before' => $beforeDescription,
                        'after' => $afterDescription,
                    ]
                );
            }
        }

        return response()->json(['message' => 'Inventory detail updated successfully', 'detail' => $detail]);
    }

    // GET /api/admin/inventory/details/{id}/history
    public function detailHistory($id)
    {
        $detail = PropertyInventoryDetail::with([
            'inventory',
            'drDetail.deliveryReceipt.requestedByUser',
            'drDetail.deliveryReceipt.receivedByUser',
            'propertyType',
            'propertyStatus',
            'condition',
        ])->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Inventory detail not found'], 404);
        }

        $history = [];

        $pushEvent = function (
            string $type,
            string $action,
            string $title,
            ?string $description,
            ?string $occurredAt,
            ?array $actor,
            ?array $meta = null
        ) use (&$history) {
            if (!$occurredAt) {
                return;
            }

            $history[] = [
                'type' => $type,
                'action' => $action,
                'title' => $title,
                'description' => $description,
                'occurred_at' => $occurredAt,
                'actor' => $actor,
                'meta' => $meta,
            ];
        };

        $drDetail = $detail->drDetail;
        if ($drDetail && $drDetail->deliveryReceipt) {
            $dr = $drDetail->deliveryReceipt;
            $drActor = $dr->receivedByUser
                ? ['id' => (int) $dr->receivedByUser->UserID, 'name' => (string) $dr->receivedByUser->FullName]
                : null;
            $drDescriptionParts = array_filter([
                $dr->DRNumber ? 'DR ' . $dr->DRNumber : null,
                $dr->SupplierName ? 'Supplier: ' . $dr->SupplierName : null,
                $dr->DateReceived ? 'Date received: ' . $dr->DateReceived : null,
            ]);
            $pushEvent(
                'delivery_receipt',
                'created',
                'Delivery receipt recorded',
                implode(' | ', $drDescriptionParts) ?: null,
                $dr->CreatedDate ?? $dr->DateReceived,
                $drActor,
                [
                    'dr_id' => (int) $dr->DRID,
                    'dr_number' => $dr->DRNumber,
                    'supplier' => $dr->SupplierName,
                ]
            );
        }

        $iarDetail = null;
        if (!empty($detail->SourceIARDetailID)) {
            $iarDetail = IARDetail::with(['iar.inspectedByUser', 'iar.acceptedByUser'])
                ->find((int) $detail->SourceIARDetailID);
        }

        if (!$iarDetail && !empty($detail->SourceDRDetailID)) {
            $iarDetail = IARDetail::with(['iar.inspectedByUser', 'iar.acceptedByUser'])
                ->where('DRDetailID', (int) $detail->SourceDRDetailID)
                ->orderByDesc('IARDetailID')
                ->first();
        }

        if ($iarDetail && $iarDetail->iar) {
            $iar = $iarDetail->iar;
            if (!empty($iar->DateInspected)) {
                $inspectedBy = $iar->inspectedByUser
                    ? ['id' => (int) $iar->inspectedByUser->UserID, 'name' => (string) $iar->inspectedByUser->FullName]
                    : null;
                $pushEvent(
                    'inspection',
                    'inspected',
                    'Inspection completed',
                    $iar->IARNumber ? 'IAR ' . $iar->IARNumber : null,
                    (string) $iar->DateInspected,
                    $inspectedBy,
                    [
                        'iar_id' => (int) $iar->IARID,
                        'iar_number' => $iar->IARNumber,
                    ]
                );
            }

            if (!empty($iar->DateAccepted)) {
                $acceptedBy = $iar->acceptedByUser
                    ? ['id' => (int) $iar->acceptedByUser->UserID, 'name' => (string) $iar->acceptedByUser->FullName]
                    : null;
                $pushEvent(
                    'inspection',
                    'accepted',
                    'Inspection accepted',
                    $iar->IARNumber ? 'IAR ' . $iar->IARNumber : null,
                    (string) $iar->DateAccepted,
                    $acceptedBy,
                    [
                        'iar_id' => (int) $iar->IARID,
                        'iar_number' => $iar->IARNumber,
                    ]
                );
            }
        }

        $inventoryCreatedAt = $detail->CreatedDate ?? $detail->inventory?->CreatedDate;
        $inventoryDescriptionParts = array_filter([
            $detail->inventory?->InventoryNumber ? 'Inventory ' . $detail->inventory->InventoryNumber : null,
            $detail->PropertyNumber ? 'Property # ' . $detail->PropertyNumber : null,
        ]);
        $pushEvent(
            'inventory',
            'created',
            'Inventory item created',
            implode(' | ', $inventoryDescriptionParts) ?: null,
            $inventoryCreatedAt,
            null,
            [
                'inventory_id' => (int) $detail->InventoryID,
                'inventory_detail_id' => (int) $detail->InventoryDetailID,
            ]
        );

        $issuanceDetails = PropertyIssuanceDetail::with([
            'issuance.issuedBy',
            'issuance.issuedTo',
            'issuance.deliveryReceipt',
            'issuance.request',
        ])
            ->where('InventoryDetailID', (int) $detail->InventoryDetailID)
            ->whereHas('issuance', function ($q) {
                $q->where('Status', 'acknowledged')->whereNull('deleted_at');
            })
            ->orderBy('IssuanceDetailID')
            ->get();

        foreach ($issuanceDetails as $issuanceDetail) {
            $issuance = $issuanceDetail->issuance;
            if (!$issuance) {
                continue;
            }

            $issuedBy = $issuance->issuedBy
                ? ['id' => (int) $issuance->issuedBy->UserID, 'name' => (string) $issuance->issuedBy->FullName]
                : null;
            $issuedToName = $issuance->issuedTo?->FullName ?? $issuance->IssuedToName ?? null;
            $issueDescriptionParts = array_filter([
                $issuance->DocumentNumber ? $issuance->DocumentNumber : null,
                $issuance->DocumentType ? $issuance->DocumentType : null,
                $issuedToName ? 'Issued to: ' . $issuedToName : null,
            ]);

            $pushEvent(
                'issuance',
                'issued',
                'Issued for accountability',
                implode(' | ', $issueDescriptionParts) ?: null,
                $issuance->DateIssued ? (string) $issuance->DateIssued : $issuance->CreatedDate,
                $issuedBy,
                [
                    'issuance_id' => (int) $issuance->IssuanceID,
                    'document_number' => $issuance->DocumentNumber,
                    'document_type' => $issuance->DocumentType,
                    'status' => $issuance->Status,
                ]
            );

            if (!empty($issuance->AcknowledgedAt)) {
                $acknowledgedBy = $issuance->AcknowledgedBy
                    ? ['id' => (int) $issuance->AcknowledgedBy, 'name' => (string) ($issuedToName ?? 'Acknowledged')]
                    : null;
                $pushEvent(
                    'issuance',
                    'acknowledged',
                    'Acknowledged by custodian',
                    $issuance->DocumentNumber ? $issuance->DocumentNumber : null,
                    (string) $issuance->AcknowledgedAt,
                    $acknowledgedBy,
                    [
                        'issuance_id' => (int) $issuance->IssuanceID,
                        'document_number' => $issuance->DocumentNumber,
                    ]
                );
            }
        }

        $transferItems = PropertyTransferItem::with([
            'transfer.requestedBy',
            'transfer.fromDepartment',
            'transfer.toDepartment',
            'transfer.fromEndUser',
            'transfer.toEndUser',
            'transfer.releasingReviewedBy',
            'transfer.approvedBy',
            'transfer.completedBy',
        ])
            ->where('InventoryDetailID', (int) $detail->InventoryDetailID)
            ->orderBy('TransferItemID')
            ->get();

        foreach ($transferItems as $transferItem) {
            $transfer = $transferItem->transfer;
            if (!$transfer) {
                continue;
            }

            $transferBaseMeta = [
                'transfer_id' => (int) $transfer->TransferID,
                'transfer_number' => $transfer->TransferNumber,
                'from_department' => $transfer->fromDepartment?->DepartmentName,
                'to_department' => $transfer->toDepartment?->DepartmentName,
                'from_end_user' => $transfer->fromEndUser?->FullName,
                'to_end_user' => $transfer->toEndUser?->FullName,
            ];

            if (!empty($transfer->created_at)) {
                $requestedBy = $transfer->requestedBy
                    ? ['id' => (int) $transfer->requestedBy->UserID, 'name' => (string) $transfer->requestedBy->FullName]
                    : null;
                $pushEvent(
                    'transfer',
                    'requested',
                    'Transfer requested',
                    $transfer->TransferNumber ? 'PTR ' . $transfer->TransferNumber : null,
                    (string) $transfer->created_at,
                    $requestedBy,
                    $transferBaseMeta
                );
            }

            if (!empty($transfer->ReleasingReviewedAt)) {
                $reviewedBy = $transfer->releasingReviewedBy
                    ? ['id' => (int) $transfer->releasingReviewedBy->UserID, 'name' => (string) $transfer->releasingReviewedBy->FullName]
                    : null;
                $decision = $transfer->ReleasingDecision ?? 'reviewed';
                $pushEvent(
                    'transfer',
                    'releasing_' . $decision,
                    'Releasing review ' . $decision,
                    $transfer->ReleasingDecisionReason ? 'Reason: ' . $transfer->ReleasingDecisionReason : null,
                    (string) $transfer->ReleasingReviewedAt,
                    $reviewedBy,
                    $transferBaseMeta
                );
            }

            if (!empty($transfer->ApprovedAt)) {
                $approvedBy = $transfer->approvedBy
                    ? ['id' => (int) $transfer->approvedBy->UserID, 'name' => (string) $transfer->approvedBy->FullName]
                    : null;
                $action = $transfer->Status === 'rejected' ? 'rejected' : 'approved';
                $pushEvent(
                    'transfer',
                    $action,
                    $transfer->Status === 'rejected' ? 'Transfer rejected' : 'Transfer approved',
                    $transfer->GsoNotes ?? null,
                    (string) $transfer->ApprovedAt,
                    $approvedBy,
                    $transferBaseMeta
                );
            }

            if (!empty($transfer->BorrowerSignedAt)) {
                $pushEvent(
                    'transfer',
                    'releasing_signed',
                    'Releasing signature confirmed',
                    null,
                    (string) $transfer->BorrowerSignedAt,
                    null,
                    $transferBaseMeta
                );
            }

            if (!empty($transfer->BorrowingSignedAt)) {
                $pushEvent(
                    'transfer',
                    'receiving_signed',
                    'Receiving signature confirmed',
                    null,
                    (string) $transfer->BorrowingSignedAt,
                    null,
                    $transferBaseMeta
                );
            }

            if (!empty($transfer->CompletedAt)) {
                $completedBy = $transfer->completedBy
                    ? ['id' => (int) $transfer->completedBy->UserID, 'name' => (string) $transfer->completedBy->FullName]
                    : null;
                $pushEvent(
                    'transfer',
                    'completed',
                    'Transfer completed',
                    null,
                    (string) $transfer->CompletedAt,
                    $completedBy,
                    $transferBaseMeta
                );
            }
        }

        $auditLogs = AssetAuditLog::with('performer')
            ->where('EntityType', 'inventory_detail')
            ->where('EntityID', (int) $detail->InventoryDetailID)
            ->orderBy('CreatedAt')
            ->get();

        foreach ($auditLogs as $log) {
            $performer = $log->performer
                ? ['id' => (int) $log->performer->UserID, 'name' => (string) $log->performer->FullName]
                : null;

            $actionLabel = ucwords(str_replace('_', ' ', (string) $log->Action));

            $pushEvent(
                'audit',
                (string) $log->Action,
                $actionLabel !== '' ? $actionLabel : 'Audit entry',
                null,
                $log->CreatedAt ? $log->CreatedAt->toISOString() : null,
                $performer,
                is_array($log->Details) ? $log->Details : null
            );
        }

        $currentDepartmentId = (int) (DB::table('assignedproperty')
            ->where('InventoryDetailID', (int) $detail->InventoryDetailID)
            ->orderByDesc(DB::raw('COALESCE(UpdatedDate, CreatedDate)'))
            ->orderByDesc('PropertyID')
            ->value('DepartmentID') ?? 0);

        if ($currentDepartmentId > 0) {
            $departmentHeadAuditLogs = AssetAuditLog::with('performer')
                ->where('EntityType', 'user')
                ->where('Action', 'department_head_assignment_changed')
                ->orderBy('CreatedAt')
                ->get();

            foreach ($departmentHeadAuditLogs as $log) {
                $details = is_array($log->Details) ? $log->Details : [];
                $fromDepartmentId = (int) ($details['from_department_id'] ?? 0);
                $toDepartmentId = (int) ($details['to_department_id'] ?? 0);

                if ($currentDepartmentId !== $fromDepartmentId && $currentDepartmentId !== $toDepartmentId) {
                    continue;
                }

                $performer = $log->performer
                    ? ['id' => (int) $log->performer->UserID, 'name' => (string) $log->performer->FullName]
                    : null;

                $person = trim((string) ($details['user_full_name'] ?? 'Department head'));
                $fromDepartmentName = trim((string) ($details['from_department_name'] ?? 'Unknown department'));
                $toDepartmentName = trim((string) ($details['to_department_name'] ?? 'Unknown department'));

                $pushEvent(
                    'accountability',
                    'department_head_assignment_changed',
                    'Department head assignment changed',
                    $person . ' moved from ' . $fromDepartmentName . ' to ' . $toDepartmentName,
                    $log->CreatedAt ? $log->CreatedAt->toISOString() : null,
                    $performer,
                    [
                        'department_id' => $currentDepartmentId,
                        'user_full_name' => $details['user_full_name'] ?? null,
                        'from_department_name' => $details['from_department_name'] ?? null,
                        'to_department_name' => $details['to_department_name'] ?? null,
                        'from_role' => $details['from_role'] ?? null,
                        'to_role' => $details['to_role'] ?? null,
                    ]
                );
            }
        }

        usort($history, function (array $a, array $b) {
            return strcmp((string) $a['occurred_at'], (string) $b['occurred_at']);
        });

        return response()->json([
            'inventory_detail_id' => (int) $detail->InventoryDetailID,
            'history' => $history,
        ]);
    }

    // POST /api/admin/inventory/backfill-qr-codes - Generate QR codes for issued items that don't have them
    public function backfillQrCodes(Request $request)
    {
        try {
            // Find all issued items (PropertyStatusID = 2) that have AssignedProperty but no QR code
            $issuedItemsWithoutQr = PropertyInventoryDetail::where('PropertyStatusID', 2)
                ->whereHas('assignedProperties', function($q) {
                    $q->where('PropertyStatusID', 2); // Issued
                })
                ->whereDoesntHave('qrCode')
                ->with(['assignedProperties' => function($q) {
                    $q->where('PropertyStatusID', 2)->latest();
                }])
                ->get();

            $created = 0;
            $errors = [];

            foreach ($issuedItemsWithoutQr as $detail) {
                try {
                    $assignedProperty = $detail->assignedProperties->first();
                    if (!$assignedProperty) continue;

                    $baseUrl = rtrim((string) config('gso.frontend_url'), '/');
                    if ($baseUrl === '') {
                        $baseUrl = 'http://localhost:3000';
                    }

                    $key = (string) config('app.key');
                    $hash = substr(hash_hmac('sha256', (string) $assignedProperty->PropertyID, $key), 0, 12);
                    $qrPayload = $baseUrl . '/qr/scan/' . $assignedProperty->PropertyID . '?h=' . $hash;

                    QrCode::create([
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'PropertyID' => (int) $assignedProperty->PropertyID,
                        'DepartmentID' => (int) $assignedProperty->DepartmentID,
                        'QRCodeData' => $qrPayload,
                        'DateGenerated' => now(),
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to create QR for InventoryDetailID {$detail->InventoryDetailID}: " . $e->getMessage();
                }
            }

            return response()->json([
                'message' => "QR codes backfilled successfully. Created $created QR codes.",
                'created' => $created,
                'errors' => $errors,
                'total_processed' => count($issuedItemsWithoutQr)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to backfill QR codes',
                'error' => $e->getMessage()
            ], 500);
        }
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
            ->leftJoin('articles', 'deliveryreceiptdetail.ArticleID', '=', 'articles.ArticleID')
            ->select(
                'propertyinventorydetail.*',
                'propertytype.PropertyName',
                'propertyclassification.ClassificationName',
                'propertyclassification.DocumentType',
                'propertyinventory.InventoryNumber',
                'propertystatus.StatusName',
                DB::raw("COALESCE(condition.ConditionName,
                    CASE
                        WHEN propertyinventorydetail.PropertyStatusID = 2 THEN 'Serviceable'
                        WHEN propertyinventorydetail.PropertyStatusID = 3 THEN 'Under Repair'
                        WHEN propertyinventorydetail.PropertyStatusID IN (4, 5) THEN 'Unserviceable'
                        ELSE 'Unknown'
                    END
                ) as ConditionName"),
                'deliveryreceiptdetail.ItemDescription as dr_item_description',
                'articles.ArticleName as dr_article_name',
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
                                'custodianName' => DB::raw("COALESCE(
                                        (
                                            SELECT euAP.FullName
                                            FROM assignedproperty ap
                                            LEFT JOIN enduser euAP ON euAP.EndUserID = ap.EndUserID
                                            WHERE ap.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                                            LIMIT 1
                                        ),
                                        (
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
                                                AND pi.Status = 'acknowledged'
                                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                            LIMIT 1
                                        ),
                                        (
                                            SELECT COALESCE(drReqU.FullName, dr.RequestedBy)
                                            FROM deliveryreceiptdetail drd
                                            JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                                            LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                                            WHERE drd.DRDetailID = propertyinventorydetail.SourceDRDetailID
                                            LIMIT 1
                                        )
                                ) as custodianName"),
                                'locationDepartment' => DB::raw("COALESCE(
                                        (
                                            SELECT depAP.DepartmentName
                                            FROM assignedproperty ap
                                            LEFT JOIN department depAP ON depAP.DepartmentID = ap.DepartmentID
                                            WHERE ap.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                                            LIMIT 1
                                        ),
                                        (
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
                                                AND pi.Status = 'acknowledged'
                                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                            LIMIT 1
                                        ),
                                        (
                                            SELECT depDR.DepartmentName
                                            FROM deliveryreceiptdetail drd
                                            JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                                            LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                                            LEFT JOIN department depDR ON depDR.DepartmentID = drReqU.DepartmentID
                                            WHERE drd.DRDetailID = propertyinventorydetail.SourceDRDetailID
                                            LIMIT 1
                                        )
                                ) as locationDepartment"),
                                'locationDepartmentId' => DB::raw("COALESCE(
                                        (
                                            SELECT ap.DepartmentID
                                            FROM assignedproperty ap
                                            WHERE ap.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            ORDER BY COALESCE(ap.UpdatedDate, ap.CreatedDate) DESC, ap.PropertyID DESC
                                            LIMIT 1
                                        ),
                                        (
                                            SELECT COALESCE(depReq.DepartmentID, dep.DepartmentID, depU.DepartmentID)
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
                                                AND pi.Status = 'acknowledged'
                                            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                            LIMIT 1
                                        ),
                                        (
                                            SELECT depDR.DepartmentID
                                            FROM deliveryreceiptdetail drd
                                            JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                                            LEFT JOIN user drReqU ON drReqU.UserID = dr.RequestedBy
                                            LEFT JOIN department depDR ON depDR.DepartmentID = drReqU.DepartmentID
                                            WHERE drd.DRDetailID = propertyinventorydetail.SourceDRDetailID
                                            LIMIT 1
                                        )
                                ) as locationDepartmentId"),
                                'lastIssuedDate' => DB::raw("(
                                        SELECT pi.DateIssued
                                        FROM propertyissuancedetail pidet
                                        JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
                                        WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            AND pi.deleted_at IS NULL
                                            AND pidet.deleted_at IS NULL
                                            AND pi.Status = 'acknowledged'
                                        ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
                                        LIMIT 1
                                ) as lastIssuedDate"),
                                'currentBorrowerName' => DB::raw("(
                                        SELECT COALESCE(beu.FullName, bu.FullName)
                                        FROM propertyborrowitems pbi
                                        JOIN propertyborrow pb ON pb.BorrowID = pbi.BorrowID
                                        LEFT JOIN enduser beu ON beu.EndUserID = pb.ToEndUserID
                                        LEFT JOIN user bu ON bu.UserID = pb.RequestedByUserID
                                        WHERE pbi.InventoryDetailID = propertyinventorydetail.InventoryDetailID
                                            AND pb.deleted_at IS NULL
                                            AND pbi.deleted_at IS NULL
                                            AND pb.Status IN ('borrowed','partially_returned')
                                            AND NOT EXISTS (
                                                SELECT 1
                                                FROM propertyreturnitems pri
                                                JOIN propertyreturn pr ON pr.ReturnID = pri.ReturnID
                                                WHERE pri.BorrowItemID = pbi.BorrowItemID
                                                    AND pri.deleted_at IS NULL
                                                    AND pr.deleted_at IS NULL
                                            )
                                        ORDER BY pb.BorrowedAt DESC, pb.BorrowID DESC
                                        LIMIT 1
                                ) as currentBorrowerName"),
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('propertyinventorydetail.Brand', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.ModelNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.SerialNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.PropertyNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventory.InventoryNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertytype.PropertyName', 'like', '%' . $search . '%')
                  ->orWhere('deliveryreceiptdetail.ItemDescription', 'like', '%' . $search . '%')
                  ->orWhere('articles.ArticleName', 'like', '%' . $search . '%');
            });
        }

        if ($statusId) {
            $query->where('propertyinventorydetail.PropertyStatusID', $statusId);
        }

        if ($propertyTypeId) {
            $query->where('propertyinventorydetail.PropertyTypeID', $propertyTypeId);
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $departmentIds = collect($paginator->items())
            ->pluck('locationDepartmentId')
            ->filter(fn ($id) => !empty($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $departmentHeadByDepartment = [];
        if ($departmentIds->isNotEmpty()) {
            $headRows = DB::table('user as u')
                ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
                ->where('u.IsActive', 1)
                ->where('ur.RoleName', 'Department Head')
                ->whereIn('u.DepartmentID', $departmentIds->all())
                ->orderBy('u.DepartmentID')
                ->orderByDesc(DB::raw('COALESCE(u.UpdatedDate, u.CreatedDate)'))
                ->orderByDesc('u.UserID')
                ->get(['u.DepartmentID', 'u.FullName']);

            foreach ($headRows as $row) {
                $deptId = (int) ($row->DepartmentID ?? 0);
                if ($deptId > 0 && !isset($departmentHeadByDepartment[$deptId])) {
                    $departmentHeadByDepartment[$deptId] = trim((string) ($row->FullName ?? ''));
                }
            }
        }

        $paginator->getCollection()->transform(function ($item) use ($departmentHeadByDepartment) {
            $departmentId = (int) ($item->locationDepartmentId ?? 0);
            $headName = $departmentId > 0 ? ($departmentHeadByDepartment[$departmentId] ?? null) : null;
            $hasHead = is_string($headName) && trim($headName) !== '';

            $item->currentDepartmentHead = $hasHead ? $headName : null;
            $item->departmentHeadVacant = $departmentId > 0 && !$hasHead;
            $item->accountabilityLabel = $item->departmentHeadVacant
                ? 'No current department head'
                : ($item->currentDepartmentHead ?? null);

            return $item;
        });

        return $paginator;
    }
}
