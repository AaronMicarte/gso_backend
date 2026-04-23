<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\PropertyIssuanceDetail;
use App\Models\PropertyTransferItem;
use App\Models\PropertyBorrowItem;
use App\Models\AssetAuditLog;
use App\Models\PropertyCardEntry;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\Procurement\IARDetail;
use App\Models\MasterData\PropertyStatus;
use App\Services\PropertyCardLedgerService;
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

        $resolvedDrIdSql = "COALESCE(
            propertyinventory.DRID,
            (
                SELECT drd.DRID
                FROM propertyinventorydetail invd
                JOIN deliveryreceiptdetail drd ON drd.DRDetailID = invd.SourceDRDetailID
                WHERE invd.InventoryID = propertyinventory.InventoryID
                ORDER BY drd.DRDetailID ASC
                LIMIT 1
            )
        )";

        $resolvedDrNumberSql = "COALESCE(
            (
                SELECT dr.DRNumber
                FROM deliveryreceipt dr
                WHERE dr.DRID = propertyinventory.DRID
                LIMIT 1
            ),
            (
                SELECT dr.DRNumber
                FROM propertyinventorydetail invd
                JOIN deliveryreceiptdetail drd ON drd.DRDetailID = invd.SourceDRDetailID
                JOIN deliveryreceipt dr ON dr.DRID = drd.DRID
                WHERE invd.InventoryID = propertyinventory.InventoryID
                ORDER BY drd.DRDetailID ASC
                LIMIT 1
            )
        )";

        $query = PropertyInventory::query()
            ->select('propertyinventory.*')
            ->addSelect(DB::raw($custodianSql . ' as CurrentCustodianName'))
            ->addSelect(DB::raw($locationSql . ' as CurrentLocation'))
            ->addSelect(DB::raw($locationDepartmentIdSql . ' as CurrentLocationDepartmentID'))
            ->addSelect(DB::raw($lastIssuedDateSql . ' as LastIssuedDate'))
            ->addSelect(DB::raw($resolvedDrIdSql . ' as ResolvedDRID'))
            ->addSelect(DB::raw($resolvedDrNumberSql . ' as ResolvedDRNumber'))
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
                  })
                  ->orWhereExists(function ($sub) use ($search) {
                      $sub->select(DB::raw(1))
                          ->from('propertyinventorydetail as invd')
                          ->join('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'invd.SourceDRDetailID')
                          ->join('deliveryreceipt as dr', 'dr.DRID', '=', 'drd.DRID')
                          ->whereColumn('invd.InventoryID', 'propertyinventory.InventoryID')
                          ->where('dr.DRNumber', 'like', '%' . $search . '%');
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

            if (!empty($inventory->ResolvedDRID) && !empty($inventory->ResolvedDRNumber)) {
                $inventory->ResolvedDeliveryReceipt = [
                    'DRID' => (int) $inventory->ResolvedDRID,
                    'DRNumber' => (string) $inventory->ResolvedDRNumber,
                ];
            } else {
                $inventory->ResolvedDeliveryReceipt = null;
            }

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
        $historyFingerprints = [];

        $normalizeMeta = function ($value) use (&$normalizeMeta) {
            if (is_array($value)) {
                $isAssoc = array_keys($value) !== range(0, count($value) - 1);
                if ($isAssoc) {
                    ksort($value);
                }
                foreach ($value as $k => $v) {
                    $value[$k] = $normalizeMeta($v);
                }
            }
            return $value;
        };

        $pushEvent = function (
            string $type,
            string $action,
            string $title,
            ?string $description,
            ?string $occurredAt,
            ?array $actor,
            ?array $meta = null
        ) use (&$history, &$historyFingerprints, $normalizeMeta) {
            if (!$occurredAt) {
                return;
            }

            $normalizedMeta = $meta ? $normalizeMeta($meta) : null;
            $fingerprint = implode('|', [
                $type,
                $action,
                (string) $occurredAt,
                (string) ($actor['id'] ?? 0),
                trim((string) $title),
                trim((string) ($description ?? '')),
                json_encode($normalizedMeta),
            ]);

            if (isset($historyFingerprints[$fingerprint])) {
                return;
            }
            $historyFingerprints[$fingerprint] = true;

            $history[] = [
                'type' => $type,
                'action' => $action,
                'title' => $title,
                'description' => $description,
                'occurred_at' => $occurredAt,
                'actor' => $actor,
                'meta' => $normalizedMeta,
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

        $borrowItems = PropertyBorrowItem::with([
            'borrow.requestedBy',
            'borrow.fromDepartment',
            'borrow.toDepartment',
            'borrow.fromEndUser',
            'borrow.toEndUser',
            'borrow.approvedBy',
            'borrow.returns.returnedBy',
            'borrow.returns.items',
        ])
            ->where('InventoryDetailID', (int) $detail->InventoryDetailID)
            ->orderBy('BorrowItemID')
            ->get();

        foreach ($borrowItems as $borrowItem) {
            $borrow = $borrowItem->borrow;
            if (!$borrow) {
                continue;
            }

            $borrowBaseMeta = [
                'borrow_id' => (int) $borrow->BorrowID,
                'borrow_number' => $borrow->BorrowNumber,
                'from_department' => $borrow->fromDepartment?->DepartmentName,
                'to_department' => $borrow->toDepartment?->DepartmentName,
                'from_end_user' => $borrow->fromEndUser?->FullName,
                'to_end_user' => $borrow->toEndUser?->FullName,
            ];

            if (!empty($borrow->created_at)) {
                $requestedBy = $borrow->requestedBy
                    ? ['id' => (int) $borrow->requestedBy->UserID, 'name' => (string) $borrow->requestedBy->FullName]
                    : null;
                $pushEvent(
                    'borrow',
                    'requested',
                    'Borrow requested',
                    $borrow->BorrowNumber ? 'PBR ' . $borrow->BorrowNumber : null,
                    (string) $borrow->created_at,
                    $requestedBy,
                    $borrowBaseMeta
                );
            }

            if (!empty($borrow->ApprovedAt)) {
                $approvedBy = $borrow->approvedBy
                    ? ['id' => (int) $borrow->approvedBy->UserID, 'name' => (string) $borrow->approvedBy->FullName]
                    : null;
                $pushEvent(
                    'borrow',
                    $borrow->Status === 'rejected' ? 'rejected' : 'approved',
                    $borrow->Status === 'rejected' ? 'Borrow rejected' : 'Borrow approved',
                    $borrow->GsoNotes ?? null,
                    (string) $borrow->ApprovedAt,
                    $approvedBy,
                    $borrowBaseMeta
                );
            }

            if (!empty($borrow->BorrowedAt)) {
                $pushEvent(
                    'borrow',
                    'delivered',
                    'Borrow delivered',
                    null,
                    (string) $borrow->BorrowedAt,
                    null,
                    $borrowBaseMeta
                );
            }

            foreach (($borrow->returns ?? collect()) as $returnRecord) {
                foreach (($returnRecord->items ?? collect()) as $returnItem) {
                    if ((int) $returnItem->InventoryDetailID !== (int) $detail->InventoryDetailID) {
                        continue;
                    }

                    $returnedBy = $returnRecord->returnedBy
                        ? ['id' => (int) $returnRecord->returnedBy->UserID, 'name' => (string) $returnRecord->returnedBy->FullName]
                        : null;

                    $pushEvent(
                        'borrow',
                        'returned',
                        'Borrow item returned',
                        !empty($returnRecord->ReturnNumber) ? 'Return ' . $returnRecord->ReturnNumber : null,
                        (string) ($returnRecord->ReturnedAt ?? $returnRecord->created_at),
                        $returnedBy,
                        array_merge($borrowBaseMeta, [
                            'return_id' => (int) ($returnRecord->ReturnID ?? 0),
                            'quantity_returned' => (int) ($returnItem->QuantityReturned ?? 0),
                        ])
                    );
                }
            }

            if ($borrow->Status === 'completed' && !empty($borrow->updated_at)) {
                $pushEvent(
                    'borrow',
                    'completed',
                    'Borrow completed',
                    null,
                    (string) $borrow->updated_at,
                    null,
                    $borrowBaseMeta
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

    // GET /api/admin/inventory/details/{id}/property-card
    public function propertyCard($id)
    {
        $detail = PropertyInventoryDetail::with([
            'inventory',
            'drDetail.deliveryReceipt.requestedByUser',
            'drDetail.article',
            'propertyType',
        ])->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Inventory detail not found'], 404);
        }

        app(PropertyCardLedgerService::class)->syncForInventoryDetail((int) $detail->InventoryDetailID);

        $entries = PropertyCardEntry::query()
            ->where('InventoryDetailID', (int) $detail->InventoryDetailID)
            ->orderByRaw("CASE WHEN SourceType = 'opening' THEN 0 ELSE 1 END")
            ->orderBy('EntryDate')
            ->orderBy('EntryID')
            ->get();

        $balance = 0;
        $fixedAmount = $detail->UnitCost !== null ? (float) $detail->UnitCost : null;
        $rows = $entries->map(function (PropertyCardEntry $entry) use (&$balance, $detail, $fixedAmount) {
            $rawReceiptQty = (int) ($entry->ReceiptQty ?? 0);
            $rawIssueQty = (int) ($entry->IssueQty ?? 0);

            // Represent returns as receipt movements to keep ledger columns audit-friendly.
            $receiptQty = $rawReceiptQty;
            $issueQty = $rawIssueQty;

            // Running balance: Previous Balance + Receipt - Issue
            $balance += $receiptQty;
            $balance -= $issueQty;
            if ($balance < 0) {
                $balance = 0;
            }

            $officeOfficer = $this->resolvePropertyCardOfficeOfficer($entry, $detail);

            return [
                'date' => $entry->EntryDate ? $entry->EntryDate->toDateString() : null,
                'reference' => $entry->ReferenceNo,
                'receipt_qty' => $receiptQty > 0 ? $receiptQty : null,
                'issue_qty' => $issueQty !== 0 ? $issueQty : null,
                'office_officer' => $officeOfficer,
                'balance_qty' => $balance,
                // Amount is fixed per item (unit cost), not recalculated per transaction row.
                'amount' => $fixedAmount,
                'remarks' => $entry->Remarks,
                'entry_type' => $entry->EntryType,
                'source_type' => $entry->SourceType,
            ];
        })->values();

        $entityName = trim((string) config('gso.entity_name', 'CITY GOVERNMENT OF EL SALVADOR'));
        if ($entityName === '') {
            $entityName = 'CITY GOVERNMENT OF EL SALVADOR';
        }

        $fundCluster = trim((string) config('gso.fund_cluster', ''));

        $articleName = $detail->drDetail?->article?->ArticleName
            ?? $detail->propertyType?->PropertyName
            ?? 'Property Item';

        $descriptionParts = array_values(array_filter([
            trim((string) ($detail->Description ?? '')),
            !empty($detail->Brand) ? ('Brand: ' . trim((string) $detail->Brand)) : null,
            !empty($detail->ModelNumber) ? ('Model: ' . trim((string) $detail->ModelNumber)) : null,
            !empty($detail->SerialNumber) ? ('Serial No.: ' . trim((string) $detail->SerialNumber)) : null,
        ], static fn ($value) => is_string($value) && $value !== ''));

        $composedDescription = !empty($descriptionParts)
            ? implode(' | ', $descriptionParts)
            : null;

        return response()->json([
            'inventory_detail_id' => (int) $detail->InventoryDetailID,
            'header' => [
                'entity_name' => $entityName,
                'fund_cluster' => $fundCluster,
                'property_plant_equipment' => $articleName,
                'property_number' => $detail->PropertyNumber,
                'description' => $composedDescription,
                'unit_cost' => $detail->UnitCost !== null ? (float) $detail->UnitCost : null,
            ],
            'rows' => $rows,
        ]);
    }

    private function resolvePropertyCardOfficeOfficer(PropertyCardEntry $entry, PropertyInventoryDetail $detail): string
    {
        $existing = trim((string) ($entry->OfficeOfficer ?? ''));
        $genericLabels = [
            'Borrowing office',
            'Returned to accountable office',
            'Transfer receiving office',
            'Receiving office',
            'Issued custodian',
        ];

        if ($existing !== '' && !in_array($existing, $genericLabels, true)) {
            return $existing;
        }

        if ($entry->SourceType === 'opening') {
            return $existing !== '' ? $existing : 'GSO';
        }

        if ($entry->SourceType !== 'audit' || empty($entry->SourceID)) {
            return $existing !== '' ? $existing : 'Supply and Property Unit';
        }

        $audit = DB::table('asset_audit_logs')
            ->where('AuditID', (int) $entry->SourceID)
            ->first(['Action', 'Details']);

        if (!$audit) {
            return $existing !== '' ? $existing : 'Supply and Property Unit';
        }

        $details = [];
        if (is_string($audit->Details) && $audit->Details !== '') {
            $decoded = json_decode($audit->Details, true);
            if (is_array($decoded)) {
                $details = $decoded;
            }
        }

        $action = strtolower((string) ($audit->Action ?? ''));

        if ($action === 'issued') {
            $issuanceId = (int) ($details['issuance_id'] ?? 0);
            if ($issuanceId > 0) {
                $issuanceLabel = $this->issuanceOfficeOfficer($issuanceId);
                if ($issuanceLabel !== null && $issuanceLabel !== '') {
                    return $issuanceLabel;
                }
            }

            $requestId = (int) ($details['request_id'] ?? 0);
            if ($requestId > 0) {
                $requester = DB::table('assetrequest as ar')
                    ->leftJoin('user as u', 'u.UserID', '=', 'ar.RequestedBy')
                    ->leftJoin('department as d', 'd.DepartmentID', '=', 'u.DepartmentID')
                    ->where('ar.RequestID', $requestId)
                    ->selectRaw("TRIM(CONCAT(COALESCE(u.FullName, ''), CASE WHEN d.DepartmentName IS NOT NULL AND d.DepartmentName <> '' THEN CONCAT(' - ', d.DepartmentName) ELSE '' END)) as label")
                    ->value('label');

                if (is_string($requester) && trim($requester) !== '') {
                    return trim($requester);
                }
            }

            $issuedTo = trim((string) ($details['issued_to'] ?? ''));
            if ($issuedTo !== '') {
                return $issuedTo;
            }
        }

        if ($action === 'transfer_completed') {
            return $this->departmentHeadWithDepartment((int) ($details['to_department_id'] ?? 0), 'Receiving Department');
        }

        if ($action === 'borrow_requested') {
            $borrowId = (int) ($details['borrow_id'] ?? 0);
            if ($borrowId > 0) {
                $borrowOffice = $this->borrowReceivingOfficeOfficer($borrowId);
                if ($borrowOffice !== null && $borrowOffice !== '') {
                    return $borrowOffice;
                }
            }

            return $this->departmentHeadWithDepartment((int) ($details['to_department_id'] ?? 0), 'Borrowing Department');
        }

        if ($action === 'borrow_returned') {
            $borrowId = (int) ($details['borrow_id'] ?? 0);
            if ($borrowId > 0) {
                $borrowOffice = $this->borrowReceivingOfficeOfficer($borrowId);
                if ($borrowOffice !== null && $borrowOffice !== '') {
                    return $borrowOffice;
                }
            }

            return $this->departmentHeadWithDepartment((int) ($details['to_department_id'] ?? 0), 'Borrowing Department');
        }

        return $existing !== '' ? $existing : 'Supply and Property Unit';
    }

    private function departmentHeadWithDepartment(int $departmentId, string $fallback): string
    {
        if ($departmentId <= 0) {
            return $fallback;
        }

        $headLabel = DB::table('user as u')
            ->leftJoin('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
            ->leftJoin('department as d', 'd.DepartmentID', '=', 'u.DepartmentID')
            ->where('u.DepartmentID', $departmentId)
            ->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
            ->orderByDesc('u.UserID')
            ->selectRaw("TRIM(CONCAT(COALESCE(u.FullName, ''), CASE WHEN d.DepartmentName IS NOT NULL AND d.DepartmentName <> '' THEN CONCAT(' - ', d.DepartmentName) ELSE '' END)) as label")
            ->value('label');

        if (is_string($headLabel) && trim($headLabel) !== '') {
            return trim($headLabel);
        }

        $departmentName = DB::table('department')
            ->where('DepartmentID', $departmentId)
            ->value('DepartmentName');

        if (is_string($departmentName) && trim($departmentName) !== '') {
            return trim($departmentName);
        }

        return $fallback;
    }

    private function issuanceOfficeOfficer(int $issuanceId): ?string
    {
        $issuance = DB::table('propertyissuance as pi')
            ->leftJoin('enduser as eu', 'eu.EndUserID', '=', 'pi.IssuedTo')
            ->leftJoin('department as d', 'd.DepartmentID', '=', 'eu.DepartmentID')
            ->leftJoin('assetrequest as ar', 'ar.RequestID', '=', 'pi.RequestID')
            ->leftJoin('user as req', 'req.UserID', '=', 'ar.RequestedBy')
            ->where('pi.IssuanceID', $issuanceId)
            ->selectRaw('pi.IssuedToName, eu.DepartmentID as EndUserDepartmentID, req.DepartmentID as RequestDepartmentID')
            ->first();

        if (!$issuance) {
            return null;
        }

        $deptId = (int) ($issuance->EndUserDepartmentID ?? $issuance->RequestDepartmentID ?? 0);
        if ($deptId > 0) {
            $deptHead = $this->departmentHeadWithDepartment($deptId, '');
            if ($deptHead !== '') {
                return $deptHead;
            }
        }

        $issuedToName = trim((string) ($issuance->IssuedToName ?? ''));
        return $issuedToName !== '' ? $issuedToName : null;
    }

    private function borrowReceivingOfficeOfficer(int $borrowId): ?string
    {
        $borrow = DB::table('propertyborrow as pb')
            ->leftJoin('enduser as eu', 'eu.EndUserID', '=', 'pb.ToEndUserID')
            ->leftJoin('department as d', 'd.DepartmentID', '=', 'pb.ToDepartmentID')
            ->where('pb.BorrowID', $borrowId)
            ->selectRaw('pb.ToDepartmentID, eu.FullName as EndUserName, d.DepartmentName')
            ->first();

        if (!$borrow) {
            return null;
        }

        $toDepartmentId = (int) ($borrow->ToDepartmentID ?? 0);
        if ($toDepartmentId > 0) {
            $deptHead = $this->departmentHeadWithDepartment($toDepartmentId, '');
            if ($deptHead !== '') {
                return $deptHead;
            }
        }

        $endUserName = trim((string) ($borrow->EndUserName ?? ''));
        $departmentName = trim((string) ($borrow->DepartmentName ?? ''));
        if ($endUserName !== '' || $departmentName !== '') {
            return trim(implode(' - ', array_filter([$endUserName, $departmentName])));
        }

        return null;
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
        $search = trim((string) $request->query('search', ''));
        $search = $search !== '' ? $search : null;
        $statusId = $request->filled('property_status_id') ? (int) $request->query('property_status_id') : null;
        $propertyTypeId = $request->filled('property_type_id') ? (int) $request->query('property_type_id') : null;

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
                  ->orWhere('propertyinventorydetail.Description', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventorydetail.Remarks', 'like', '%' . $search . '%')
                  ->orWhere('propertyinventory.InventoryNumber', 'like', '%' . $search . '%')
                  ->orWhere('propertytype.PropertyName', 'like', '%' . $search . '%')
                  ->orWhere('propertyclassification.ClassificationName', 'like', '%' . $search . '%')
                  ->orWhere('deliveryreceiptdetail.ItemDescription', 'like', '%' . $search . '%')
                  ->orWhere('deliveryreceiptdetail.Brand', 'like', '%' . $search . '%')
                  ->orWhere('deliveryreceiptdetail.ModelNumber', 'like', '%' . $search . '%')
                  ->orWhere('deliveryreceiptdetail.SerialNumber', 'like', '%' . $search . '%')
                  ->orWhere('articles.ArticleName', 'like', '%' . $search . '%');
            });
        }

        if ($statusId !== null && $statusId > 0) {
            $query->where('propertyinventorydetail.PropertyStatusID', $statusId);
        }

        if ($propertyTypeId !== null && $propertyTypeId > 0) {
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
