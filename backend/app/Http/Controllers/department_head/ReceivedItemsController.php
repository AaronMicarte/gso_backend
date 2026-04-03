<?php

namespace App\Http\Controllers\department_head;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivedItemsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ((int) ($user->UserRoleID ?? 0) !== 4) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $departmentId = (int) ($user->DepartmentID ?? 0);
            if ($departmentId <= 0) {
                return response()->json(['message' => 'Department not found'], 422);
            }

            $perPage = (int) $request->input('per_page', 15);
            $search = trim((string) $request->input('search', ''));

            $drQuery = DB::table('deliveryreceipt as dr')
                ->join('user as reqU', 'dr.RequestedBy', '=', 'reqU.UserID')
                ->leftJoin('department as dept', 'reqU.DepartmentID', '=', 'dept.DepartmentID')
                ->leftJoin('inspectionacceptancereport as iar', function ($join) {
                    $join->on('iar.DRID', '=', 'dr.DRID')
                        ->whereNull('iar.deleted_at');
                })
                ->whereNull('dr.deleted_at')
                ->where('reqU.DepartmentID', $departmentId)
                ->where('dr.InspectionStatus', 'inspected')
                ->select(
                    'dr.DRID',
                    'dr.DRNumber',
                    'dr.DateReceived',
                    'dr.InspectionStatus',
                    'reqU.FullName as RequestedBy',
                    'dept.DepartmentName',
                    'iar.IARID',
                    'iar.IARNumber',
                    'iar.DateAccepted as IARDateAccepted'
                )
                ->orderBy('dr.DateReceived', 'desc');

            if ($search !== '') {
                $like = '%' . $search . '%';
                $drQuery->where(function ($query) use ($like) {
                    $query->where('dr.DRNumber', 'like', $like)
                        ->orWhere('iar.IARNumber', 'like', $like)
                        ->orWhere('reqU.FullName', 'like', $like)
                        ->orWhere('dept.DepartmentName', 'like', $like);
                });
            }

            $drPage = $drQuery->paginate($perPage);
            $drIds = $drPage->getCollection()->pluck('DRID')->filter()->values()->all();

            if (empty($drIds)) {
                return response()->json($drPage);
            }

            $items = DB::table('deliveryreceiptdetail as drd')
                ->join('deliveryreceipt as dr', 'drd.DRID', '=', 'dr.DRID')
                ->leftJoin('inspectionacceptancereport as iar', function ($join) {
                    $join->on('iar.DRID', '=', 'dr.DRID')
                        ->whereNull('iar.deleted_at');
                })
                ->leftJoin('iardetail as iard', function ($join) {
                    $join->on('iard.DRDetailID', '=', 'drd.DRDetailID')
                        ->on('iard.IARID', '=', 'iar.IARID');
                })
                ->leftJoin('propertytype as pt', 'drd.PropertyTypeID', '=', 'pt.PropertyTypeID')
                ->leftJoin('propertyclassification as pc', 'pt.PropertyClassificationID', '=', 'pc.PropertyClassificationID')
                ->leftJoin('propertyinventorydetail as pid', 'pid.SourceDRDetailID', '=', 'drd.DRDetailID')
                ->leftJoin('propertystatus as ps', 'pid.PropertyStatusID', '=', 'ps.PropertyStatusID')
                ->leftJoin('propertyissuancedetail as pidet', 'pidet.InventoryDetailID', '=', 'pid.InventoryDetailID')
                ->leftJoin('propertyissuance as pi', 'pi.IssuanceID', '=', 'pidet.IssuanceID')
                ->whereNull('drd.deleted_at')
                ->whereIn('drd.DRID', $drIds)
                ->select(
                    'drd.DRID',
                    'drd.DRDetailID',
                    'drd.ItemDescription',
                    'drd.Brand',
                    'drd.ModelNumber',
                    'drd.SerialNumber',
                    'drd.QuantityDelivered',
                    'pc.DocumentType',
                    DB::raw('MAX(iard.QuantityAccepted) as QuantityAccepted'),
                    DB::raw('MAX(pi.Status) as IssuanceStatus'),
                    DB::raw('MAX(ps.StatusName) as InventoryStatusName'),
                    DB::raw("SUM(CASE WHEN ps.StatusName IN ('For issuance', 'For Issuance', 'To be issued', 'Available') THEN 1 ELSE 0 END) as ForIssuanceCount"),
                    DB::raw("SUM(CASE WHEN ps.StatusName = 'Issued' THEN 1 ELSE 0 END) as IssuedCount")
                )
                ->groupBy(
                    'drd.DRID',
                    'drd.DRDetailID',
                    'drd.ItemDescription',
                    'drd.Brand',
                    'drd.ModelNumber',
                    'drd.SerialNumber',
                    'drd.QuantityDelivered',
                    'pc.DocumentType'
                )
                ->get();

            $itemsByDr = $items->groupBy('DRID');

            $result = $drPage->getCollection()->map(function ($dr) use ($itemsByDr) {
                $drItems = $itemsByDr->get($dr->DRID, collect());

                $mappedItems = $drItems->map(function ($item) use ($dr) {
                    $issuanceStatus = (string) ($item->IssuanceStatus ?? '');
                    $status = 'to_be_issued';
                    if ($issuanceStatus === 'acknowledged') {
                        $status = 'acknowledged';
                    }

                    $labels = [
                        'to_be_issued' => 'To be issued',
                        'acknowledged' => 'Acknowledged',
                    ];

                    $statusLabel = $labels[$status] ?? 'To be issued';

                    return [
                        'DRDetailID' => (int) $item->DRDetailID,
                        'ItemDescription' => $item->ItemDescription,
                        'Brand' => $item->Brand,
                        'ModelNumber' => $item->ModelNumber,
                        'SerialNumber' => $item->SerialNumber,
                        'QuantityDelivered' => (int) ($item->QuantityDelivered ?? 0),
                        'QuantityAccepted' => (int) ($item->QuantityAccepted ?? 0),
                        'DocumentType' => $item->DocumentType ?? 'ICS',
                        'status' => $status,
                        'status_label' => $statusLabel,
                    ];
                })->values()->all();

                return [
                    'DRID' => (int) $dr->DRID,
                    'DRNumber' => $dr->DRNumber,
                    'DateReceived' => $dr->DateReceived,
                    'InspectionStatus' => $dr->InspectionStatus,
                    'RequestedBy' => $dr->RequestedBy,
                    'DepartmentName' => $dr->DepartmentName,
                    'IARID' => $dr->IARID,
                    'IARNumber' => $dr->IARNumber,
                    'IARDateAccepted' => $dr->IARDateAccepted,
                    'items' => $mappedItems,
                ];
            });

            $drPage->setCollection($result);

            return response()->json($drPage);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch received items',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }
}
