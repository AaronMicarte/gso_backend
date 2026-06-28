<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyIssuanceAssignmentController extends Controller
{
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
