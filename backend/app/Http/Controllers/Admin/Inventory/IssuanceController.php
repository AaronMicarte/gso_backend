<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\PropertyIssuance;
use App\Models\Inventory\PropertyIssuanceDetail;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\QrCode;

class IssuanceController extends Controller
{
    // POST /api/admin/inventory/issuances
    public function store(Request $request)
    {
        $request->validate([
            'issued_by' => 'required|integer',
            'issued_to' => 'required|integer',
            'issued_to_type' => 'nullable|in:department,enduser',
            'date_issued' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'details.*.quantity_issued' => 'required|integer|min:1',
        ]);

        // Resolve IssuedTo -> DepartmentID
        $issuedTo = $request->input('issued_to');
        $issuedToType = $request->input('issued_to_type', 'department');
        $departmentId = null;

        if ($issuedToType === 'department') {
            $dept = \App\Models\MasterData\Department::find($issuedTo);
            if (!$dept) {
                return response()->json(['message' => 'IssuedTo department not found.'], 422);
            }
            $departmentId = $dept->DepartmentID;
        } else { // enduser
            $enduser = \App\Models\MasterData\EndUser::find($issuedTo);
            if (!$enduser) {
                return response()->json(['message' => 'IssuedTo end user not found.'], 422);
            }
            if (!$enduser->DepartmentID) {
                return response()->json(['message' => 'End user has no department assigned.'], 422);
            }
            $departmentId = $enduser->DepartmentID;
        }

        DB::beginTransaction();
        try {
            $issuance = PropertyIssuance::create([
                'IssuedBy' => $request->input('issued_by'),
                'IssuedTo' => $issuedTo,
                'DateIssued' => $request->input('date_issued'),
                'Remarks' => $request->input('remarks') ?? null,
                'CreatedDate' => now(),
            ]);

            // Track quantity reductions so repeated InventoryDetailIDs are handled
            $quantityAdjustments = [];

            foreach ($request->input('details') as $d) {
                $inventoryDetailId = $d['inventory_detail_id'];
                $qtyIssued = (int) $d['quantity_issued'];

                $inventoryDetail = PropertyInventoryDetail::lockForUpdate()->find($inventoryDetailId);
                if (!$inventoryDetail) {
                    DB::rollBack();
                    return response()->json(['message' => 'Inventory detail not found: ' . $inventoryDetailId], 422);
                }

                $available = (int) $inventoryDetail->Quantity;
                if ($available < $qtyIssued) {
                    DB::rollBack();
                    return response()->json(['message' => "Insufficient quantity for InventoryDetailID {$inventoryDetailId}. Available: {$available}, requested: {$qtyIssued}"], 422);
                }

                // decrement available quantity
                $inventoryDetail->Quantity = $available - $qtyIssued;
                $inventoryDetail->save();

                $detail = PropertyIssuanceDetail::create([
                    'IssuanceID' => $issuance->IssuanceID,
                    'InventoryDetailID' => $inventoryDetailId,
                    'QuantityIssued' => $qtyIssued,
                    'Remarks' => $d['remarks'] ?? null,
                ]);

                // Generate QR for this inventory detail and record department context
                $payload = rtrim(config('app.url') ?? env('APP_URL'), '/') . '/inventory/scan/' . $inventoryDetail->InventoryDetailID . '?h=' . substr(sha1($inventoryDetail->InventoryDetailID . '|' . now()->timestamp), 0, 12);

                QrCode::updateOrCreate(
                    ['InventoryDetailID' => $inventoryDetail->InventoryDetailID],
                    [
                        'PropertyID' => null,
                        'DepartmentID' => $departmentId,
                        'QRCodeData' => $payload,
                        'DateGenerated' => now(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => 'Issuance recorded and QR generated for issued items', 'issuance_id' => $issuance->IssuanceID], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to record issuance: ' . $e->getMessage()], 500);
        }
    }
}
