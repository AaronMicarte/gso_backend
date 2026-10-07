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
use App\Models\Auth\User;
use App\Models\AssetAuditLog;
use App\Services\Notifications\RoleNotificationService;

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
                    $resolvedEndUser = EndUser::where('DepartmentID', $departmentId)->orderBy('EndUserID')->first();

                    if (!$resolvedEndUser) {
                        $deptHead = \App\Models\Auth\User::where('DepartmentID', $departmentId)
                            ->where('IsActive', 1)
                            ->whereIn('UserRoleID', [4, 5])
                            ->orderByDesc('UpdatedDate')
                            ->first();

                        $resolvedEndUser = EndUser::create([
                            'PositionID' => 2,
                            'DepartmentID' => $departmentId,
                            'FullName' => $deptHead?->FullName ?? 'Department Custodian',
                            'Email' => $deptHead?->Email ?? null,
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

                $actorId = (int) ($request->input('issued_by') ?? 0);
                if ($actorId > 0) {
                    AssetAuditLog::createLog(
                        'inventory_detail',
                        (int) $inventoryDetail->InventoryDetailID,
                        'issued',
                        $actorId,
                        [
                            'issuance_id' => (int) $issuance->IssuanceID,
                            'document_number' => $issuance->IssuanceID,
                            'issued_to' => (int) $issuedTo,
                            'issued_to_type' => $issuedToType,
                        ]
                    );
                }
            }

            DB::commit();

            $recipientUserIds = $this->getIssuanceNotificationUserIds(
                (int) $departmentId,
                (int) ($request->input('issued_by') ?? 0)
            );

            app(RoleNotificationService::class)->notifyUsers(
                $recipientUserIds,
                'inventory_issuance.created',
                'Inventory Issuance Recorded',
                sprintf('Issuance #%s has been recorded with %d item(s).', (string) $issuance->IssuanceID, count($request->input('details'))),
                [
                    'issuance_id' => (string) $issuance->IssuanceID,
                    'issued_to_type' => (string) $issuedToType,
                    'issued_to' => (string) $issuedTo,
                    'link' => '/inventory/issuances/' . $issuance->IssuanceID,
                ]
            );

            return response()->json(['message' => 'Issuance recorded successfully', 'issuance_id' => $issuance->IssuanceID], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to record issuance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @return array<int>
     */
    private function getIssuanceNotificationUserIds(int $departmentId, ?int $excludeUserId = null): array
    {
        $query = User::query()
            ->select('UserID')
            ->where('IsActive', true)
            ->where(function ($q) use ($departmentId) {
                $q->whereHas('userRole', function ($roleQ) {
                    $roleQ->whereIn('RoleName', ['Admin', 'GSO Staff']);
                })->orWhere(function ($deptHeadQ) use ($departmentId) {
                    $deptHeadQ->whereHas('userRole', function ($roleQ) {
                        $roleQ->where('RoleName', 'Department Head');
                    });

                    if ($departmentId > 0) {
                        $deptHeadQ->where('DepartmentID', $departmentId);
                    } else {
                        $deptHeadQ->whereRaw('1 = 0');
                    }
                });
            });

        if (!empty($excludeUserId)) {
            $query->where('UserID', '!=', (int) $excludeUserId);
        }

        return $query->pluck('UserID')->map(fn ($id) => (int) $id)->all();
    }
}
