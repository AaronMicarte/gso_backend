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
        $rows = DB::table('property_issuance_assignments')
            ->where('issuance_id', $id)
            ->orderBy('id')
            ->get();

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
                        $oldOffice = trim($oldRow->office_name ?? '');
                        $newName = $data['receiver_name'];
                        $newPos = trim($data['position_office'] ?? '');
                        $newOffice = trim($data['office_name'] ?? '');

                        // If ANY receiver field changed, track it for global propagation
                        if ($oldName !== '' && (
                            mb_strtolower($oldName) !== mb_strtolower($newName) ||
                            mb_strtolower($oldPos) !== mb_strtolower($newPos) ||
                            mb_strtolower($oldOffice) !== mb_strtolower($newOffice)
                        )) {
                            $updates[mb_strtolower($oldName)] = [
                                'receiver_name' => $newName,
                                'position_office' => $data['position_office'],
                                'office_name' => $data['office_name'],
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

            // Propagate receiver info changes globally across ALL issuances
            foreach ($updates as $oldNameLower => $newInfo) {
                DB::table('property_issuance_assignments')
                    ->whereRaw('LOWER(TRIM(receiver_name)) = ?', [$oldNameLower])
                    ->where(function ($q) use ($newInfo) {
                        $q->where('receiver_name', '!=', $newInfo['receiver_name'])
                          ->orWhere(DB::raw('COALESCE(position_office, \'\')'), '!=', $newInfo['position_office'] ?? '')
                          ->orWhere(DB::raw('COALESCE(office_name, \'\')'), '!=', $newInfo['office_name'] ?? '');
                    })
                    ->update([
                        'receiver_name' => $newInfo['receiver_name'],
                        'position_office' => $newInfo['position_office'],
                        'office_name' => $newInfo['office_name'],
                        'updated_at' => $now,
                    ]);
            }
        });

        return response()->json(['message' => 'Assignments saved']);
    }
}