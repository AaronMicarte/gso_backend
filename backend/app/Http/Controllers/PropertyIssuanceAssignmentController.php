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
            'assignments.*.issuance_detail_id' => 'required|integer',
            'assignments.*.receiver_name' => 'required|string',
            'assignments.*.position_office' => 'nullable|string',
            'assignments.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $assignments = $payload['assignments'];

        DB::transaction(function () use ($id, $assignments) {
            $now = now();

            // Collect detail IDs from the payload
            $detailIds = array_values(array_unique(array_map(
                fn ($a) => (int) $a['issuance_detail_id'],
                $assignments
            )));

            // Delete any rows for this issuance OR any row pointing at these detail IDs
            // (covers rows left behind from earlier issuance edits)
            DB::table('property_issuance_assignments')
                ->where('issuance_id', $id)
                ->orWhereIn('issuance_detail_id', $detailIds)
                ->delete();

            // Dedup payload by issuance_detail_id — last one wins
            $byDetail = [];
            foreach ($assignments as $a) {
                $byDetail[(int) $a['issuance_detail_id']] = [
                    'issuance_id' => $id,
                    'issuance_detail_id' => (int) $a['issuance_detail_id'],
                    'receiver_name' => $a['receiver_name'],
                    'position_office' => $a['position_office'] ?? null,
                    'quantity' => (int) $a['quantity'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($byDetail)) {
                DB::table('property_issuance_assignments')->insert(array_values($byDetail));
            }
        });

        return response()->json(['message' => 'Assignments saved']);
    }
}