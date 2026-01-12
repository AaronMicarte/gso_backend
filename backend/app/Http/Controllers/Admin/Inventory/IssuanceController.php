<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\PropertyIssuance;
use App\Models\Inventory\PropertyIssuanceDetail;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\AssignedProperty;
use App\Models\EndUser;

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
            $enduser = EndUser::find($issuedTo);
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

                if ($qtyIssued !== 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Issuance quantities must be 1 per InventoryDetailID for QR/accountability tracking.'], 422);
                }

                // Ensure we have an EndUser for assignedproperty
                $resolvedEndUser = null;
                if ($issuedToType === 'enduser') {
                    $resolvedEndUser = $enduser;
                } else {
                    $resolvedEndUser = EndUser::where('DepartmentID', $departmentId)
                        ->where('FullName', 'Department Custodian')
                        ->first();

                    if (!$resolvedEndUser) {
                        $resolvedEndUser = EndUser::create([
                            'PositionID' => 2,
                            'DepartmentID' => $departmentId,
                            'FullName' => 'Department Custodian',
                            'IsActive' => 1,
                        ]);
                    }
                }

                $assignedProperty = AssignedProperty::where('InventoryDetailID', $inventoryDetail->InventoryDetailID)->first();
                $assignedPayload = [
                    'InventoryDetailID' => $inventoryDetail->InventoryDetailID,
                    'EndUserID' => (int) $resolvedEndUser->EndUserID,
                    'DepartmentID' => (int) $departmentId,
                    'PropertyCardNumber' => $issuance->IssuanceID,
                    'Description' => $inventoryDetail->Description,
                    'AssignedDate' => $request->input('date_issued'),
                    'PropertyStatusID' => 2,
                    'ConditionID' => (int) ($inventoryDetail->ConditionID ?? 1),
                    'Remarks' => $request->input('remarks') ?? null,
                ];

                if ($assignedProperty) {
                    $assignedProperty->fill($assignedPayload);
                    $assignedProperty->save();
                } else {
                    $assignedProperty = AssignedProperty::create($assignedPayload);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Issuance recorded successfully', 'issuance_id' => $issuance->IssuanceID], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to record issuance: ' . $e->getMessage()], 500);
        }
    }
}
