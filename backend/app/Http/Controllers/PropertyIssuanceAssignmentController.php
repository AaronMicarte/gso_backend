<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyIssuanceAssignmentController extends Controller
{
    public function getReceiverSuggestions(Request $request)
    {
        $search = trim($request->input('query', $request->input('search', '')));

        $query = DB::table('property_issuance_assignments')
            ->select('receiver_name', 'position_office')
            ->whereNotNull('receiver_name')
            ->whereRaw("TRIM(receiver_name) != ''");

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('receiver_name', 'like', "%{$search}%")
                  ->orWhere('position_office', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('id', 'desc')->get();

        $seen = [];
        $results = [];
        $namesWithPositions = [];

        foreach ($records as $row) {
            $name = trim($row->receiver_name ?? '');
            $pos = trim($row->position_office ?? '');
            if ($name !== '' && $pos !== '') {
                $namesWithPositions[mb_strtolower($name)] = true;
            }
        }

        foreach ($records as $row) {
            $name = trim($row->receiver_name ?? '');
            $pos = trim($row->position_office ?? '');
            if ($name === '') continue;

            if ($pos === '' && !empty($namesWithPositions[mb_strtolower($name)])) {
                continue;
            }

            $key = mb_strtolower($name) . '||' . mb_strtolower($pos);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $results[] = [
                    'receiver_name' => $name,
                    'position_office' => $pos !== '' ? $pos : null,
                ];
            }
        }

        if (count($results) > 100) {
            $results = array_slice($results, 0, 100);
        }

        return response()->json(['data' => $results]);
    }

    public function index($id)
    {
        // Sync any transferred items belonging to this issuance with the latest department and end user
        $transferredItems = DB::table('propertyissuancedetail as pid')
            ->join('propertytransferdetails as ptd', 'ptd.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->join('propertytransfer as pt', 'pt.TransferID', '=', 'ptd.TransferID')
            ->leftJoin('department as d', 'd.DepartmentID', '=', 'pt.ToDepartmentID')
            ->leftJoin('enduser as eu', 'eu.EndUserID', '=', 'pt.ToEndUserID')
            ->where('pid.IssuanceID', $id)
            ->where('pt.Status', 'completed')
            ->whereNull('pid.deleted_at')
            ->whereNull('ptd.deleted_at')
            ->whereNull('pt.deleted_at')
            ->orderBy('pt.CompletedAt', 'desc')
            ->orderBy('pt.TransferID', 'desc')
            ->select([
                'pid.IssuanceDetailID',
                'd.DepartmentName',
                'eu.FullName',
            ])
            ->get()
            ->unique('IssuanceDetailID');

        foreach ($transferredItems as $tItem) {
            $assignmentUpdate = [];
            if (!empty($tItem->DepartmentName)) {
                $assignmentUpdate['office_name'] = $tItem->DepartmentName;
            }
            if (!empty($tItem->FullName)) {
                $assignmentUpdate['receiver_name'] = trim($tItem->FullName);
            }
            if (!empty($assignmentUpdate)) {
                DB::table('property_issuance_assignments')
                    ->where('issuance_detail_id', $tItem->IssuanceDetailID)
                    ->update($assignmentUpdate);
            }
        }

        $rows = DB::table('property_issuance_assignments')
            ->where('issuance_id', $id)
            ->orderBy('id')
            ->get();

        // Build dept-head-name lookup per unique office_name so the frontend
        // "Assign to Dept Head" button resolves the correct head after transfers.
        $officeNames = $rows->pluck('office_name')
            ->filter(fn($v) => trim((string) $v) !== '')
            ->unique()
            ->values();
        $deptHeadByOffice = [];
        foreach ($officeNames as $office) {
            $officeLower = strtolower(trim((string) $office));
            if (isset($deptHeadByOffice[$officeLower])) {
                continue;
            }
            $dept = DB::table('department')
                ->whereRaw("LOWER(TRIM(DepartmentName)) = ?", [$officeLower])
                ->where('IsActive', 1)
                ->first(['DepartmentID']);
            if (!$dept) {
                continue;
            }
            // Try user table (department head role)
            $headName = DB::table('user as u')
                ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
                ->where('u.DepartmentID', (int) $dept->DepartmentID)
                ->where('u.IsActive', 1)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
                      ->orWhere('u.UserRoleID', 4);
                })
                ->value('u.FullName');
            if (!$headName) {
                // Fallback: enduser table with department-head position
                $headName = DB::table('enduser as eu')
                    ->leftJoin('position as p', 'p.PositionID', '=', 'eu.PositionID')
                    ->where('eu.DepartmentID', (int) $dept->DepartmentID)
                    ->where('eu.IsActive', 1)
                    ->where(function ($q) {
                        $q->where('eu.PositionID', 2)
                          ->orWhereRaw("LOWER(COALESCE(p.PositionName, '')) = ?", ['department head']);
                    })
                    ->value('eu.FullName');
            }
            if ($headName) {
                $deptHeadByOffice[$officeLower] = trim((string) $headName);
            }
        }

        $rows = $rows->map(function ($row) use ($deptHeadByOffice) {
            $officeKey = strtolower(trim((string) ($row->office_name ?? '')));
            $row->dept_head_name = $deptHeadByOffice[$officeKey] ?? null;
            return $row;
        });

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request, $id)
    {
        $payload = $request->all();

        $validator = Validator::make($payload, [
            'assignments' => 'required|array|min:1',
            'assignments.*.id' => 'nullable|integer',
            'assignments.*.issuance_detail_id' => 'required|integer',
            'assignments.*.receiver_name' => 'required|string',
            'assignments.*.position_office' => 'nullable|string',
            'assignments.*.office_name' => 'nullable|string',
            'assignments.*.is_department_head' => 'nullable|boolean',
            'assignments.*.quantity' => 'required|integer|min:1',
            'deleted_ids' => 'nullable|array',
            'deleted_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $assignments = $payload['assignments'];
        $deletedIds = $payload['deleted_ids'] ?? [];

        // Check that any provided row IDs belong to this issuance_id
        $providedIds = [];
        foreach ($assignments as $a) {
            if (!empty($a['id'])) {
                $providedIds[] = (int) $a['id'];
            }
        }

        if (!empty($providedIds)) {
            $validCount = DB::table('property_issuance_assignments')
                ->where('issuance_id', $id)
                ->whereIn('id', $providedIds)
                ->count();

            if ($validCount !== count(array_unique($providedIds))) {
                return response()->json(['message' => 'One or more assignment IDs do not belong to this issuance record.'], 422);
            }
        }

        DB::transaction(function () use ($id, $assignments, $deletedIds) {
            $now = now();

            // Delete only rows belonging to this issuance whose id is in explicit deleted_ids
            if (!empty($deletedIds)) {
                DB::table('property_issuance_assignments')
                    ->where('issuance_id', $id)
                    ->whereIn('id', array_map('intval', $deletedIds))
                    ->delete();
            }

            // Track receiver info changes so we can propagate globally
            $updates = []; // oldNameLower => { receiver_name, position_office, office_name }

            foreach ($assignments as $a) {
                $isDeptHead = filter_var($a['is_department_head'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $rowId = !empty($a['id']) ? (int) $a['id'] : null;

                $data = [
                    'issuance_id' => (int) $id,
                    'issuance_detail_id' => (int) $a['issuance_detail_id'],
                    'receiver_name' => trim((string) $a['receiver_name']),
                    'position_office' => isset($a['position_office']) && trim((string) $a['position_office']) !== ''
                        ? trim((string) $a['position_office'])
                        : ($isDeptHead ? 'Department Head' : null),
                    'office_name' => isset($a['office_name']) && trim((string) $a['office_name']) !== ''
                        ? trim((string) $a['office_name'])
                        : null,
                    'is_department_head' => $isDeptHead ? 1 : 0,
                    'quantity' => (int) $a['quantity'],
                    'updated_at' => $now,
                ];

                if ($rowId) {
                    // Fetch old row to detect any changes
                    $oldRow = DB::table('property_issuance_assignments')
                        ->where('id', $rowId)
                        ->where('issuance_id', $id)
                        ->first();

                    if ($oldRow) {
                        $oldName = trim($oldRow->receiver_name ?? '');
                        $oldPos = trim($oldRow->position_office ?? '');
                        $newName = $data['receiver_name'];
                        $newPos = trim($data['position_office'] ?? '');

                        // If name or position changed, track for global propagation
                        // (office_name is NOT propagated — different issuances can have different offices due to transfers)
                        if ($oldName !== '' && (
                            mb_strtolower($oldName) !== mb_strtolower($newName) ||
                            mb_strtolower($oldPos) !== mb_strtolower($newPos)
                        )) {
                            $updates[mb_strtolower($oldName)] = [
                                'receiver_name' => $newName,
                                'position_office' => $data['position_office'],
                            ];
                        }
                    }

                    DB::table('property_issuance_assignments')
                        ->where('id', $rowId)
                        ->where('issuance_id', $id)
                        ->update($data);
                } else {
                    $data['created_at'] = $now;
                    DB::table('property_issuance_assignments')->insert($data);
                }
            }

            // Propagate receiver name/position changes globally across ALL issuances
            // office_name is intentionally excluded — it varies per issuance (transfers, etc.)
            foreach ($updates as $oldNameLower => $newInfo) {
                DB::table('property_issuance_assignments')
                    ->whereRaw('LOWER(TRIM(receiver_name)) = ?', [$oldNameLower])
                    ->where(function ($q) use ($newInfo) {
                        $q->where('receiver_name', '!=', $newInfo['receiver_name'])
                          ->orWhere(DB::raw('COALESCE(position_office, \'\')'), '!=', $newInfo['position_office'] ?? '');
                    })
                    ->update([
                        'receiver_name' => $newInfo['receiver_name'],
                        'position_office' => $newInfo['position_office'],
                        'updated_at' => $now,
                    ]);
            }
        });

        return response()->json(['message' => 'Assignments saved']);
    }
}