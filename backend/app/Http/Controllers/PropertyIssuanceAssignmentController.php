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

        // Delete existing assignments for issuance and re-insert
        DB::transaction(function () use ($id, $assignments) {
            DB::table('property_issuance_assignments')->where('issuance_id', $id)->delete();
            $now = now();
            $rows = [];
            foreach ($assignments as $a) {
                $rows[] = [
                    'issuance_id' => $id,
                    'issuance_detail_id' => $a['issuance_detail_id'],
                    'receiver_name' => $a['receiver_name'],
                    'position_office' => $a['position_office'] ?? null,
                    'quantity' => $a['quantity'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($rows)) DB::table('property_issuance_assignments')->insert($rows);
        });

        return response()->json(['message' => 'Assignments saved']);
    }
}
