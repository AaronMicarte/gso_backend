<?php

namespace App\Http\Controllers;

use App\Models\Auth\User;
use App\Models\MasterData\Department;
use App\Models\PropertyRlsddpItem;
use App\Models\PropertyRlsddpReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyRlsddpController extends Controller
{
    /**
     * Display a listing of RLSDDP reports.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = PropertyRlsddpReport::query()
            ->with([
                'department:DepartmentID,DepartmentName,DepartmentCode',
                'items.inventoryDetail',
                'createdBy:UserID,FullName',
                'updatedBy:UserID,FullName',
            ])
            ->orderByDesc('RlsddpID');

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('RlsddpNumber', 'like', "%{$search}%")
                  ->orWhere('AccountableOfficer', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('DepartmentName', 'like', "%{$search}%")
                         ->orWhere('DepartmentCode', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('DepartmentID', (int) $request->query('department_id'));
        }

        if ($request->filled('status')) {
            $query->where('Status', (string) $request->query('status'));
        }

        $perPage = (int) $request->query('per_page', 15);
        $reports = $query->paginate($perPage);

        return response()->json($reports);
    }

    /**
     * Display the specified RLSDDP report with full item details.
     */
    public function show(int $id): JsonResponse
    {
        $report = PropertyRlsddpReport::with([
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'items.inventoryDetail.drDetail.article',
            'removedItems.inventoryDetail.drDetail.article',
            'createdBy:UserID,FullName',
            'updatedBy:UserID,FullName',
        ])->findOrFail($id);

        foreach ($report->items as $item) {
            if (empty($item->Description)) {
                $item->Description = $this->generateItemDescription($item->InventoryDetailID);
            }
        }

        foreach ($report->removedItems as $item) {
            if (empty($item->Description)) {
                $item->Description = $this->generateItemDescription($item->InventoryDetailID);
            }
        }

        // Resolve PAR / ICS document number and date from the first item
        $parNumber = null;
        $parDate   = null;

        $firstItem = $report->items->first();
        if ($firstItem) {
            $invDetailId = $firstItem->InventoryDetailID;

            // Try PAR first
            $parRow = DB::table('propertyinventorydetail as pid')
                ->leftJoin('assignedproperty as ap', 'ap.InventoryDetailID', '=', 'pid.InventoryDetailID')
                ->leftJoin('pardetail as pard', 'pard.PropertyID', '=', 'ap.PropertyID')
                ->leftJoin('par as p', 'p.PARID', '=', 'pard.PARID')
                ->where('pid.InventoryDetailID', $invDetailId)
                ->whereNotNull('p.PARNumber')
                ->select(['p.PARNumber', 'p.DateIssued as PARDate'])
                ->first();

            if ($parRow && !empty($parRow->PARNumber)) {
                $parNumber = $parRow->PARNumber;
                $parDate   = $parRow->PARDate;
            } else {
                // Try ICS / property issuance
                $issRow = DB::table('propertyinventorydetail as pid')
                    ->leftJoin('propertyissuancedetail as pissd', 'pissd.InventoryDetailID', '=', 'pid.InventoryDetailID')
                    ->leftJoin('propertyissuance as piss', 'piss.IssuanceID', '=', 'pissd.IssuanceID')
                    ->where('pid.InventoryDetailID', $invDetailId)
                    ->whereNotNull('piss.DocumentNumber')
                    ->select(['piss.DocumentNumber', 'piss.DocumentType', 'piss.DateIssued as ICSDate'])
                    ->first();

                if ($issRow && !empty($issRow->DocumentNumber)) {
                    $docType   = !empty($issRow->DocumentType) ? strtoupper($issRow->DocumentType) : 'ICS';
                    $parNumber = str_starts_with($issRow->DocumentNumber, $docType)
                        ? $issRow->DocumentNumber
                        : "{$docType}-{$issRow->DocumentNumber}";
                    $parDate = $issRow->ICSDate;
                }
            }
        }

        $data                = $report->toArray();
        $data['PARNumber']   = $parNumber;
        $data['PARDate']     = $parDate;

        return response()->json($data);
    }

    /**
     * Get list of PAR & ICS documents with department, accountable officer, and assigned assets.
     */
    public function documents(Request $request): JsonResponse
    {
        $query = DB::table('propertyinventorydetail as pid')
            ->join('propertyinventory as pi', 'pi.InventoryID', '=', 'pid.InventoryID')
            ->leftJoin('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'pid.SourceDRDetailID')
            ->leftJoin('articles as a', 'a.ArticleID', '=', 'drd.ArticleID')
            ->leftJoin('assignedproperty as ap', 'ap.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->leftJoin('pardetail as pard', 'pard.PropertyID', '=', 'ap.PropertyID')
            ->leftJoin('par as p', 'p.PARID', '=', 'pard.PARID')
            ->leftJoin('propertyissuancedetail as pissd', 'pissd.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->leftJoin('propertyissuance as piss', 'piss.IssuanceID', '=', 'pissd.IssuanceID')
            ->leftJoin('property_issuance_assignments as pia', 'pia.issuance_detail_id', '=', 'pissd.IssuanceDetailID')
            ->leftJoin('user as u', 'u.UserID', '=', 'piss.IssuedTo')
            ->leftJoin('department as d', function ($j) {
                $j->on('d.DepartmentID', '=', 'ap.DepartmentID')
                  ->orOn('d.DepartmentID', '=', 'u.DepartmentID');
            })
            ->whereNull('pid.deleted_at');

        $rawItems = $query->select([
            'pid.InventoryDetailID',
            'pid.PropertyNumber',
            'pid.UnitCost',
            'pid.TotalCost',
            'pid.SourceDRDetailID',
            'pid.InventoryID',
            'pid.SerialNumber as pid_SerialNumber',
            'a.ArticleName',
            'drd.ItemDescription',
            'drd.Brand',
            'drd.ModelNumber',
            'drd.SerialNumber as drd_SerialNumber',
            'p.PARNumber',
            'piss.DocumentType',
            'piss.DocumentNumber',
            'piss.IssuedToName',
            'pia.receiver_name',
            'u.FullName as UserFullName',
            'd.DepartmentID',
            'd.DepartmentName',
        ])->get();

        $documentsMap = [];

        foreach ($rawItems as $item) {
            $docType = !empty($item->DocumentType) ? strtoupper($item->DocumentType) : ($item->UnitCost >= 50000 ? 'PAR' : 'ICS');
            $rawPar = !empty($item->PARNumber) ? $item->PARNumber : (!empty($item->DocumentNumber) ? $item->DocumentNumber : 'N/A');

            if (empty($rawPar)) {
                continue;
            }

            $formattedParNo = ($rawPar !== 'N/A') ? (str_starts_with($rawPar, $docType) ? $rawPar : "{$docType}-{$rawPar}") : "{$docType} N/A";

            $officer = !empty($item->receiver_name)
                ? trim($item->receiver_name)
                : (!empty($item->IssuedToName)
                    ? trim($item->IssuedToName)
                    : (!empty($item->UserFullName) ? trim($item->UserFullName) : 'Unassigned'));

            $deptId = $item->DepartmentID ? (int)$item->DepartmentID : 0;
            $deptName = !empty($item->DepartmentName) ? trim($item->DepartmentName) : 'Unassigned Department';

            $articleName     = trim((string) ($item->ArticleName ?? ''));
            $itemDescription = trim((string) ($item->ItemDescription ?? ''));
            $brand           = trim((string) ($item->Brand ?? ''));
            $model           = trim((string) ($item->ModelNumber ?? ''));
            $sn              = trim((string) ($item->pid_SerialNumber ?? $item->drd_SerialNumber ?? ''));

            $descParts = array_filter([
                $articleName,
                $itemDescription,
                $brand ? "Brand: {$brand}" : null,
                $model ? "Model: {$model}" : null,
                $sn ? "SN: {$sn}" : null,
            ]);
            $generatedDescription = implode(', ', $descParts) ?: 'N/A';

            $assetObj = [
                'InventoryDetailID' => $item->InventoryDetailID,
                'ParNumber'         => $formattedParNo,
                'PropertyNumber'    => !empty($item->PropertyNumber) ? $item->PropertyNumber : 'N/A',
                'AcquisitionCost'   => (float) ($item->UnitCost ?? $item->TotalCost ?? 0.00),
                'ArticleName'       => $articleName,
                'ItemDescription'   => $itemDescription,
                'Brand'             => $brand,
                'ModelNumber'       => $model,
                'SerialNumber'      => $sn,
                'Description'       => $generatedDescription,
                'SourceDRDetailID'  => $item->SourceDRDetailID,
                'InventoryID'       => $item->InventoryID,
            ];

            if (!isset($documentsMap[$formattedParNo])) {
                $documentsMap[$formattedParNo] = [
                    'document_number'     => $formattedParNo,
                    'document_type'       => $docType,
                    'department_id'       => $deptId,
                    'department_name'     => $deptName,
                    'accountable_officer' => $officer,
                    'items'               => [],
                ];
            }

            $existingIds = array_column($documentsMap[$formattedParNo]['items'], 'InventoryDetailID');
            if (!in_array($item->InventoryDetailID, $existingIds)) {
                $documentsMap[$formattedParNo]['items'][] = $assetObj;
            }
        }

        return response()->json([
            'data' => array_values($documentsMap)
        ]);
    }

    /**
     * Store a newly created RLSDDP report in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'DepartmentID'       => 'required|integer|exists:department,DepartmentID',
            'AccountableOfficer' => 'required|string|max:255',
            'Status'             => 'required|string|in:Lost,Stolen,Damaged,Destroyed',
            'PoliceNotified'     => 'required|boolean',
            'PoliceStation'      => 'nullable|required_if:PoliceNotified,true,1|string|max:255',
            'DateReported'       => 'nullable|required_if:PoliceNotified,true,1|date|before_or_equal:today',
            'Remarks'            => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.InventoryDetailID' => 'required|integer',
            'items.*.PropertyNumber'    => 'nullable|string',
            'items.*.AcquisitionCost'   => 'nullable|numeric',
            'items.*.Description'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $rlsddpNumber = PropertyRlsddpReport::generateRlsddpNumber();
            $creatorUserId = $user?->UserID ?? Auth::id();

            $report = PropertyRlsddpReport::create([
                'RlsddpNumber'       => $rlsddpNumber,
                'DepartmentID'       => $validated['DepartmentID'],
                'AccountableOfficer' => trim($validated['AccountableOfficer']),
                'Status'             => $validated['Status'],
                'ApprovalStatus'     => 'Pending',
                'PoliceNotified'     => (bool) $validated['PoliceNotified'],
                'PoliceStation'      => $validated['PoliceNotified'] ? trim((string) ($validated['PoliceStation'] ?? '')) : null,
                'DateReported'       => $validated['PoliceNotified'] ? $validated['DateReported'] : null,
                'Remarks'            => isset($validated['Remarks']) ? trim((string) $validated['Remarks']) : null,
                'CreatedByUserID'    => (int) $creatorUserId,
            ]);

            $selectedInventoryDetailIds = [];

            foreach ($validated['items'] as $itemData) {
                $invDetailId = (int) $itemData['InventoryDetailID'];
                $selectedInventoryDetailIds[] = $invDetailId;

                $description = isset($itemData['Description']) && !empty($itemData['Description'])
                    ? trim((string) $itemData['Description'])
                    : $this->generateItemDescription($invDetailId);

                PropertyRlsddpItem::create([
                    'RlsddpID'          => $report->RlsddpID,
                    'InventoryDetailID' => $invDetailId,
                    'PropertyNumber'    => $itemData['PropertyNumber'] ?? null,
                    'AcquisitionCost'   => $itemData['AcquisitionCost'] ?? 0.00,
                    'Description'       => $description,
                ]);
            }

            return response()->json([
                'message' => 'RLSDDP report created successfully',
                'report'  => $report->load(['department', 'items', 'createdBy', 'updatedBy']),
            ], 201);
        });
    }

    /**
     * Approve a report and transfer its active items to the removed inventory list.
     */
    public function approveReport(int $id): JsonResponse
    {
       $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if ($roleName !== 'Admin') {
            return response()->json(['message' => 'Unauthorized. Only Admin can approve RLSDDP reports.'], 403);
        }
        $report = PropertyRlsddpReport::with('items')->findOrFail($id);

        if ($report->ApprovalStatus === 'Approved') {
            return response()->json([
                'message' => 'This RLSDDP report is already approved.',
                'report' => $report,
            ]);
        }

        if (!in_array($report->Status, ['Lost', 'Stolen', 'Destroyed'], true)) {
            return response()->json(['message' => 'Only Lost, Stolen, and Destroyed reports can be approved for transfer.'], 422);
        }

        return DB::transaction(function () use ($report) {
            $itemIds = $report->items()->pluck('RlsddpItemID')->toArray();

            foreach ($itemIds as $itemId) {
                $item = PropertyRlsddpItem::find($itemId);
                if (!$item) {
                    continue;
                }

                $detailId = $item->InventoryDetailID;
                $detail = \App\Models\Inventory\PropertyInventoryDetail::find($detailId);
                if ($detail) {
                    $inventoryId = $detail->InventoryID;
                    $detail->delete();

                    $activeCount = \App\Models\Inventory\PropertyInventoryDetail::where('InventoryID', $inventoryId)->count();
                    if ($activeCount === 0) {
                        $inventory = \App\Models\Inventory\PropertyInventory::find($inventoryId);
                        if ($inventory) {
                            $inventory->delete();
                        }
                    }
                }
            }

            if ($report->Status === 'Destroyed') {
                $inventoryDetailIds = $report->items()->pluck('InventoryDetailID')->toArray();
                if (!empty($inventoryDetailIds)) {
                    $this->markAssetsUnserviceable($inventoryDetailIds);
                }
            }

            $report->update([
                'ApprovalStatus' => 'Approved',
                'UpdatedByUserID' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'RLSDDP report approved and all active items were transferred to removed inventory.',
                'report' => $report->fresh(['department', 'items', 'createdBy', 'updatedBy']),
            ]);
        });
    }

    /**
     * Reject a report with rejection notes and restore its items for reselection.
     */
    public function rejectReport(Request $request, int $id): JsonResponse
    {
    $user = Auth::user();
$roleName = $user?->userRole?->RoleName ?? '';
if ($roleName !== 'Admin') {
    return response()->json(['message' => 'Unauthorized. Only Admin can reject RLSDDP reports.'], 403);
}

        $validated = $request->validate([
            'RejectionNotes' => 'required|string|min:5|max:1000',
        ]);

        $report = PropertyRlsddpReport::with('items')->findOrFail($id);

        if ($report->ApprovalStatus === 'Approved') {
            return response()->json([
                'message' => 'Cannot reject an already approved RLSDDP report.',
                'report' => $report,
            ], 422);
        }

        return DB::transaction(function () use ($report, $validated) {
            // If there were any previously soft-deleted items (from a prior approval), restore them
            $itemIds = $report->items()->pluck('RlsddpItemID')->toArray();

            foreach ($itemIds as $itemId) {
                $item = PropertyRlsddpItem::find($itemId);
                if (!$item) {
                    continue;
                }

                $detailId = $item->InventoryDetailID;
                $detail = \App\Models\Inventory\PropertyInventoryDetail::withTrashed()->find($detailId);
                if ($detail && $detail->trashed()) {
                    $detail->restore();

                    $inventoryId = $detail->InventoryID;
                    $inventory = \App\Models\Inventory\PropertyInventory::withTrashed()->find($inventoryId);
                    if ($inventory && $inventory->trashed()) {
                        $inventory->restore();
                    }
                }
            }

            // Update the report with rejection status and notes
            $report->update([
                'ApprovalStatus' => 'Rejected',
                'RejectionNotes' => trim($validated['RejectionNotes']),
                'RejectedAt' => now(),
                'UpdatedByUserID' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'RLSDDP report rejected. All items have been restored and are available for reselection.',
                'report' => $report->fresh(['department', 'items', 'createdBy', 'updatedBy']),
            ]);
        });
    }

    /**
     * Update the specified RLSDDP report.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $report = PropertyRlsddpReport::findOrFail($id);

        $validated = $request->validate([
            'DepartmentID'       => 'sometimes|required|integer|exists:department,DepartmentID',
            'AccountableOfficer' => 'sometimes|required|string|max:255',
            'Status'             => 'sometimes|required|string|in:Lost,Stolen,Damaged,Destroyed',
            'PoliceNotified'     => 'sometimes|required|boolean',
            'PoliceStation'      => 'nullable|string|max:255',
            'DateReported'       => 'nullable|date|before_or_equal:today',
            'Remarks'            => 'nullable|string',
            'items'              => 'sometimes|required|array|min:1',
            'items.*.InventoryDetailID' => 'required|integer',
            'items.*.PropertyNumber'    => 'nullable|string',
            'items.*.AcquisitionCost'   => 'nullable|numeric',
            'items.*.Description'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $report, $user) {
            $updateData = [];

            if (isset($validated['DepartmentID'])) {
                $updateData['DepartmentID'] = $validated['DepartmentID'];
            }
            if (isset($validated['AccountableOfficer'])) {
                $updateData['AccountableOfficer'] = trim($validated['AccountableOfficer']);
            }
            if (isset($validated['Status'])) {
                $updateData['Status'] = $validated['Status'];
            }
            if (array_key_exists('PoliceNotified', $validated)) {
                $updateData['PoliceNotified'] = (bool) $validated['PoliceNotified'];
                if (!$updateData['PoliceNotified']) {
                    $updateData['PoliceStation'] = null;
                    $updateData['DateReported'] = null;
                }
            }
            if (isset($validated['PoliceStation']) && ($updateData['PoliceNotified'] ?? $report->PoliceNotified)) {
                $updateData['PoliceStation'] = trim((string) $validated['PoliceStation']);
            }
            if (isset($validated['DateReported']) && ($updateData['PoliceNotified'] ?? $report->PoliceNotified)) {
                $updateData['DateReported'] = $validated['DateReported'];
            }
            if (array_key_exists('Remarks', $validated)) {
                $updateData['Remarks'] = $validated['Remarks'] ? trim((string) $validated['Remarks']) : null;
            }

            $updateData['UpdatedByUserID'] = $user ? $user->UserID : null;

            $report->update($updateData);

            if (isset($validated['items'])) {
                PropertyRlsddpItem::where('RlsddpID', $report->RlsddpID)->delete();

                $selectedInventoryDetailIds = [];
                foreach ($validated['items'] as $itemData) {
                    $invDetailId = (int) $itemData['InventoryDetailID'];
                    $selectedInventoryDetailIds[] = $invDetailId;

                    $description = isset($itemData['Description']) && !empty($itemData['Description'])
                        ? trim((string) $itemData['Description'])
                        : $this->generateItemDescription($invDetailId);

                    PropertyRlsddpItem::create([
                        'RlsddpID'          => $report->RlsddpID,
                        'InventoryDetailID' => $invDetailId,
                        'PropertyNumber'    => $itemData['PropertyNumber'] ?? null,
                        'AcquisitionCost'   => $itemData['AcquisitionCost'] ?? 0.00,
                        'Description'       => $description,
                    ]);
                }
            }

            return response()->json([
                'message' => 'RLSDDP report updated successfully',
                'report'  => $report->load(['department', 'items', 'createdBy', 'updatedBy']),
            ]);
        });
    }

    /**
     * Get list of Accountable Officers for a department.
     * ONLY Department Head receives "(Department Head)" suffix.
     * Issuance assignment users do NOT receive any suffix tag.
     */
    public function accountableOfficer(int $departmentId): JsonResponse
    {
        $officers = [];
        $seen = [];

        // 1. Department Head (Tagged with "(Department Head)")
        $deptHead = DB::table('user as u')
            ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
            ->where('u.DepartmentID', $departmentId)
            ->where('u.IsActive', 1)
            ->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
            ->orderByDesc('u.UserID')
            ->value('u.FullName');

        if (!empty($deptHead)) {
            $headClean = trim((string) $deptHead);
            $seen[strtolower($headClean)] = true;
            $officers[] = [
                'name'       => $headClean,
                'label'      => $headClean . ' (Department Head)',
                'source'     => 'Department Head',
                'type'       => 'department_head',
            ];
        }

        // 2. Property Issuance Assignments (NO tag per requirement)
        $assignmentOfficers = DB::table('property_issuance_assignments as pia')
            ->join('propertyissuance as pi', 'pi.IssuanceID', '=', 'pia.issuance_id')
            ->leftJoin('user as u', 'u.UserID', '=', 'pi.IssuedTo')
            ->leftJoin('propertyissuancedetail as pid', 'pid.IssuanceDetailID', '=', 'pia.issuance_detail_id')
            ->leftJoin('assignedproperty as ap', 'ap.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->where(function ($q) use ($departmentId) {
                $q->where('u.DepartmentID', $departmentId)
                  ->orWhere('ap.DepartmentID', $departmentId);
            })
            ->whereNotNull('pia.receiver_name')
            ->where('pia.receiver_name', '!=', '')
            ->distinct()
            ->pluck('pia.receiver_name');

        foreach ($assignmentOfficers as $name) {
            $nameClean = trim((string) $name);
            if (!empty($nameClean) && !isset($seen[strtolower($nameClean)])) {
                $seen[strtolower($nameClean)] = true;
                $officers[] = [
                    'name'       => $nameClean,
                    'label'      => $nameClean,
                    'source'     => 'Property Issuance Assignment',
                    'type'       => 'assignment',
                ];
            }
        }

        // 3. Delivery Receipt (DR) Requester
        $drRequester = DB::table('deliveryreceipt as dr')
            ->leftJoin('user as u', 'u.UserID', '=', 'dr.RequestedBy')
            ->where('u.DepartmentID', $departmentId)
            ->orderByDesc('dr.DRID')
            ->value('u.FullName');

        if (!empty($drRequester)) {
            $drClean = trim((string) $drRequester);
            if (!isset($seen[strtolower($drClean)])) {
                $seen[strtolower($drClean)] = true;
                $officers[] = [
                    'name'       => $drClean,
                    'label'      => $drClean,
                    'source'     => 'Delivery Receipt (DR) Requester',
                    'type'       => 'dr_requester',
                ];
            }
        }

        if (empty($officers)) {
            $officers[] = [
                'name'       => 'Unassigned',
                'label'      => 'Unassigned',
                'source'     => 'Default',
                'type'       => 'unassigned',
            ];
        }

        return response()->json([
            'accountable_officers' => $officers,
            'default_officer'      => $officers[0]['name'] ?? 'Unassigned',
            'default_source'       => $officers[0]['source'] ?? null,
            'default_type'         => $officers[0]['type'] ?? null,
        ]);
    }

    /**
     * Retrieve currently issued/assigned assets belonging to the selected department & officer.
     * Excludes assets that are part of approved RLSDDP reports.
     */
    public function departmentAssets(int $departmentId, Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $officerName = trim((string) $request->query('officer', ''));
        $officerType = trim((string) $request->query('officer_type', ''));

        $query = DB::table('propertyinventorydetail as pid')
            ->join('propertyinventory as pi', 'pi.InventoryID', '=', 'pid.InventoryID')
            ->leftJoin('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'pid.SourceDRDetailID')
            ->leftJoin('articles as a', 'a.ArticleID', '=', 'drd.ArticleID')
            ->leftJoin('assignedproperty as ap', 'ap.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->leftJoin('pardetail as pard', 'pard.PropertyID', '=', 'ap.PropertyID')
            ->leftJoin('par as p', 'p.PARID', '=', 'pard.PARID')
            ->leftJoin('propertyissuancedetail as pissd', 'pissd.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->leftJoin('propertyissuance as piss', 'piss.IssuanceID', '=', 'pissd.IssuanceID')
            ->leftJoin('property_issuance_assignments as pia', 'pia.issuance_detail_id', '=', 'pissd.IssuanceDetailID')
            ->leftJoin('user as u', 'u.UserID', '=', 'piss.IssuedTo')
            // Exclude assets that are part of approved RLSDDP reports
            ->leftJoin('property_rlsddp_items as pri', 'pri.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->leftJoin('property_rlsddp_reports as prr', function ($join) {
                $join->on('prr.RlsddpID', '=', 'pri.RlsddpID')
                     ->where('prr.ApprovalStatus', '=', 'Approved')
                     ->whereNull('prr.deleted_at');
            })
            ->whereNull('pid.deleted_at')
            ->whereNull('prr.RlsddpID') // Filter out approved RLSDDP assets
            ->where(function ($q) use ($departmentId) {
                $q->where('ap.DepartmentID', $departmentId)
                  ->orWhere('u.DepartmentID', $departmentId);
            });

        // Filter by specific end user if type is assignment
        if ($officerType === 'assignment' && !empty($officerName)) {
            $query->where(function ($q) use ($officerName) {
                $q->where('pia.receiver_name', 'like', "%{$officerName}%")
                  ->orWhere('piss.IssuedToName', 'like', "%{$officerName}%");
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pid.PropertyNumber', 'like', "%{$search}%")
                  ->orWhere('a.ArticleName', 'like', "%{$search}%")
                  ->orWhere('drd.ItemDescription', 'like', "%{$search}%")
                  ->orWhere('drd.Brand', 'like', "%{$search}%")
                  ->orWhere('drd.ModelNumber', 'like', "%{$search}%")
                  ->orWhere('drd.SerialNumber', 'like', "%{$search}%")
                  ->orWhere('p.PARNumber', 'like', "%{$search}%");
            });
        }

        $items = $query->select([
            'pid.InventoryDetailID',
            'pid.PropertyNumber',
            'pid.UnitCost',
            'pid.TotalCost',
            'pid.SourceDRDetailID',
            'pid.InventoryID',
            'pid.SerialNumber as pid_SerialNumber',
            'a.ArticleName',
            'drd.ItemDescription',
            'drd.Brand',
            'drd.ModelNumber',
            'drd.SerialNumber as drd_SerialNumber',
            'p.PARNumber',
            'piss.DocumentType',
            'piss.DocumentNumber',
        ])
        ->distinct()
        ->get();

        $formatted = $items->map(function ($item) {
            $articleName     = trim((string) ($item->ArticleName ?? ''));
            $itemDescription = trim((string) ($item->ItemDescription ?? ''));
            $brand           = trim((string) ($item->Brand ?? ''));
            $model           = trim((string) ($item->ModelNumber ?? ''));
            $sn              = trim((string) ($item->pid_SerialNumber ?? $item->drd_SerialNumber ?? ''));

            $docType = !empty($item->DocumentType) ? strtoupper($item->DocumentType) : ($item->UnitCost >= 50000 ? 'PAR' : 'ICS');
            $rawPar = !empty($item->PARNumber) ? $item->PARNumber : (!empty($item->DocumentNumber) ? $item->DocumentNumber : 'N/A');
            $formattedParNo = ($rawPar !== 'N/A') ? (str_starts_with($rawPar, $docType) ? $rawPar : "{$docType}-{$rawPar}") : "{$docType} N/A";

            $rawProp = !empty($item->PropertyNumber) ? $item->PropertyNumber : 'N/A';

            $descParts = array_filter([
                $articleName,
                $itemDescription,
                $brand ? "Brand: {$brand}" : null,
                $model ? "Model: {$model}" : null,
                $sn ? "SN: {$sn}" : null,
            ]);

            $generatedDescription = implode(', ', $descParts);

            return [
                'InventoryDetailID' => $item->InventoryDetailID,
                'ParNumber'         => $formattedParNo,
                'PropertyNumber'    => $rawProp,
                'AcquisitionCost'   => (float) ($item->UnitCost ?? $item->TotalCost ?? 0.00),
                'ArticleName'       => $articleName,
                'ItemDescription'   => $itemDescription,
                'Brand'             => $brand,
                'ModelNumber'       => $model,
                'SerialNumber'      => $sn,
                'Description'       => $generatedDescription ?: 'N/A',
                'SourceDRDetailID'  => $item->SourceDRDetailID,
                'InventoryID'       => $item->InventoryID,
            ];
        });

        return response()->json(['data' => $formatted]);
    }

    /**
     * Helper to generate full item description separated by comma.
     */
   private function generateItemDescription(int $inventoryDetailId): string
{
    $item = DB::table('propertyinventorydetail as pid')
        ->leftJoin('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'pid.SourceDRDetailID')
        ->leftJoin('articles as a', 'a.ArticleID', '=', 'drd.ArticleID')
        ->where('pid.InventoryDetailID', $inventoryDetailId)
        ->select([
            'a.ArticleName',
            'drd.ItemDescription',
            'drd.Brand',
            'drd.ModelNumber',
            'pid.SerialNumber as pid_SerialNumber',
        ])
        ->first();

    if (!$item) {
        return 'N/A';
    }

    $sn = trim((string) ($item->pid_SerialNumber ?? ''));

    $descParts = array_filter([
        trim((string) ($item->ArticleName ?? '')),
        trim((string) ($item->ItemDescription ?? '')),
        $item->Brand ? "Brand: {$item->Brand}" : null,
        $item->ModelNumber ? "Model: {$item->ModelNumber}" : null,
        $sn !== '' ? "SN: {$sn}" : null,
    ]);

    return implode(', ', $descParts) ?: 'N/A';
}

    /**
     * Update asset condition to Unserviceable in inventory.
     */
    private function markAssetsUnserviceable(array $inventoryDetailIds): void
    {
        if (empty($inventoryDetailIds)) {
            return;
        }

        $unserviceableId = DB::table('condition')
            ->whereRaw("LOWER(ConditionName) = ?", ['unserviceable'])
            ->value('ConditionID');

        if (!$unserviceableId) {
            $unserviceableId = 2;
        }

        DB::table('propertyinventorydetail')
            ->whereIn('InventoryDetailID', $inventoryDetailIds)
            ->update([
                'ConditionID' => $unserviceableId,
                'updated_at'  => now(),
            ]);
    }

    /**
     * Remove an RLSDDP item (soft deletes the related PropertyInventoryDetail / PropertyInventory).
     */
    public function removeItem(int $itemId): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            return response()->json(['message' => 'Unauthorized. Only Admin and Staff can perform this action.'], 403);
        }

        $rlsddpItem = PropertyRlsddpItem::findOrFail($itemId);
        $detailId = $rlsddpItem->InventoryDetailID;

        DB::transaction(function () use ($detailId) {
            $detail = \App\Models\Inventory\PropertyInventoryDetail::find($detailId);
            if ($detail) {
                $inventoryId = $detail->InventoryID;
                $detail->delete();

                // Check if any other non-deleted details exist for this inventory
                $activeCount = \App\Models\Inventory\PropertyInventoryDetail::where('InventoryID', $inventoryId)->count();
                if ($activeCount === 0) {
                    $inventory = \App\Models\Inventory\PropertyInventory::find($inventoryId);
                    if ($inventory) {
                        $inventory->delete();
                    }
                }
            }
        });

        return response()->json(['message' => 'Item removed from active inventory successfully.']);
    }

    /**
     * Restore an RLSDDP item (restores the related PropertyInventoryDetail / PropertyInventory).
     */
    public function restoreItem(int $itemId): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            return response()->json(['message' => 'Unauthorized. Only Admin and Staff can perform this action.'], 403);
        }

        $rlsddpItem = PropertyRlsddpItem::findOrFail($itemId);
        $detailId = $rlsddpItem->InventoryDetailID;

        DB::transaction(function () use ($detailId) {
            $serviceableId = DB::table('condition')
                ->whereRaw("LOWER(ConditionName) = ?", ['serviceable'])
                ->value('ConditionID') ?? 1;

            $hasAssignment = DB::table('assignedproperty')
                ->where('InventoryDetailID', $detailId)
                ->exists();

            $targetStatusId = $hasAssignment ? 2 : 1; // 2 = Issued, 1 = For issuance (never For Disposal)

            $detail = \App\Models\Inventory\PropertyInventoryDetail::withTrashed()->find($detailId);
            if ($detail) {
                $detail->restore();
                $detail->ConditionID = $serviceableId;
                $detail->PropertyStatusID = $targetStatusId;
                $detail->save();

                $inventoryId = $detail->InventoryID;
                $inventory = \App\Models\Inventory\PropertyInventory::withTrashed()->find($inventoryId);
                if ($inventory && $inventory->trashed()) {
                    $inventory->restore();
                }
            }

            if ($hasAssignment) {
                DB::table('assignedproperty')
                    ->where('InventoryDetailID', $detailId)
                    ->update([
                        'ConditionID' => $serviceableId,
                        'PropertyStatusID' => 2, // Issued
                    ]);
            }

            // Disassociate from RLSDDP items so it no longer retains RLSDDP status after restoration
            DB::table('property_rlsddp_items')
                ->where('InventoryDetailID', $detailId)
                ->delete();
        });

        return response()->json(['message' => 'Item restored to active inventory successfully.']);
    }

    /**
     * List all inventory items that have been removed via RLSDDP across all reports.
     * Returns the soft-deleted PropertyInventoryDetail rows that are linked to an RLSDDP item.
     */
    public function removedItems(Request $request): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $query = \App\Models\PropertyRlsddpItem::query()
            ->join('propertyinventorydetail as pid', function ($join) {
                $join->on('pid.InventoryDetailID', '=', 'property_rlsddp_items.InventoryDetailID')
                    ->whereNotNull('pid.deleted_at');
            })
            ->join('property_rlsddp_reports as rpt', 'rpt.RlsddpID', '=', 'property_rlsddp_items.RlsddpID')
            ->leftJoin('department as dep', 'dep.DepartmentID', '=', 'rpt.DepartmentID')
            ->whereNull('rpt.deleted_at')
            ->select([
                'property_rlsddp_items.RlsddpItemID',
                'property_rlsddp_items.RlsddpID',
                'property_rlsddp_items.InventoryDetailID',
                'property_rlsddp_items.PropertyNumber',
                'property_rlsddp_items.AcquisitionCost',
                'property_rlsddp_items.Description',
                'rpt.RlsddpNumber',
                'rpt.Status as RlsddpStatus',
                'rpt.AccountableOfficer',
                'dep.DepartmentName',
                'pid.deleted_at as removed_at',
            ])
            ->orderByDesc('pid.deleted_at');

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('property_rlsddp_items.PropertyNumber', 'like', "%{$search}%")
                  ->orWhere('rpt.RlsddpNumber', 'like', "%{$search}%")
                  ->orWhere('dep.DepartmentName', 'like', "%{$search}%")
                  ->orWhere('rpt.AccountableOfficer', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 25);
        $results = $query->paginate($perPage);

        return response()->json($results);
    }
}
