<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventoryRemovalController extends Controller
{
    /**
     * Soft-delete an inventory detail (move to Removed Items)
     */
    public function remove(int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            return response()->json(['message' => 'Unauthorized. Only Admin and Staff can perform this action.'], 403);
        }

        DB::transaction(function () use ($id) {
            $detail = \App\Models\Inventory\PropertyInventoryDetail::find($id);
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
        });

        return response()->json(['message' => 'Item removed from active inventory successfully.']);
    }

    /**
     * Restore a soft-deleted inventory detail
     */
    public function restore(int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            return response()->json(['message' => 'Unauthorized. Only Admin and Staff can perform this action.'], 403);
        }

        DB::transaction(function () use ($id) {
            $serviceableId = DB::table('condition')
                ->whereRaw("LOWER(ConditionName) = ?", ['serviceable'])
                ->value('ConditionID') ?? 1;

            $hasAssignment = DB::table('assignedproperty')
                ->where('InventoryDetailID', $id)
                ->exists();

            $targetStatusId = $hasAssignment ? 2 : 1; // 2 = Issued, 1 = For issuance (never For Disposal)

            $detail = \App\Models\Inventory\PropertyInventoryDetail::withTrashed()->find($id);
            if ($detail && $detail->trashed()) {
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
                    ->where('InventoryDetailID', $id)
                    ->update([
                        'ConditionID' => $serviceableId,
                        'PropertyStatusID' => 2, // Issued
                    ]);
            }

            // Disassociate from RLSDDP items so it no longer retains RLSDDP status after restoration
            DB::table('property_rlsddp_items')
                ->where('InventoryDetailID', $id)
                ->delete();
        });

        return response()->json(['message' => 'Item restored to active inventory successfully.']);
    }

    /**
     * List all removed inventory details (soft-deleted)
     */
    public function removedItems(Request $request): JsonResponse
{
    $user = Auth::user();
    $roleName = $user?->userRole?->RoleName ?? '';
    if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    $perPage = max(1, (int) $request->query('per_page', 25));
    $page    = max(1, (int) $request->query('page', 1));

    $base = DB::table('propertyinventorydetail as pid')
        ->join('propertyinventory as pi', 'pi.InventoryID', '=', 'pid.InventoryID')
        ->leftJoin('assignedproperty as ap', 'ap.InventoryDetailID', '=', 'pid.InventoryDetailID')
        ->leftJoin('department as ad', 'ad.DepartmentID', '=', 'ap.DepartmentID')
        ->leftJoin('propertyissuancedetail as pissd', 'pissd.InventoryDetailID', '=', 'pid.InventoryDetailID')
        ->leftJoin('propertyissuance as piss', 'piss.IssuanceID', '=', 'pissd.IssuanceID')
        ->leftJoin('user as u', 'u.UserID', '=', 'piss.IssuedTo')
        ->leftJoin('department as ud', 'ud.DepartmentID', '=', 'u.DepartmentID')
        ->whereNotNull('pid.deleted_at');

    if ($request->filled('search')) {
        $search = trim((string) $request->query('search'));
      $base->where(function ($q) use ($search) {
    $q->where('pid.PropertyNumber', 'like', "%{$search}%")
      ->orWhere('ad.DepartmentName', 'like', "%{$search}%")
      ->orWhere('ud.DepartmentName', 'like', "%{$search}%")
      ->orWhere('piss.IssuedToName', 'like', "%{$search}%");
});
    }

    try {
        // Count on a clean clone — no SELECT, no ORDER BY
        $total = (clone $base)->count('pid.InventoryDetailID');

        $items = (clone $base)
            ->orderByDesc('pid.deleted_at')
            ->select([
                'pid.InventoryDetailID',
                'pid.PropertyNumber',
                'pid.UnitCost as AcquisitionCost',
                'pid.Description',
                DB::raw('COALESCE(ad.DepartmentID, ud.DepartmentID) as department_id'),
                DB::raw('COALESCE(ad.DepartmentName, ud.DepartmentName) as department_name'),
          DB::raw("COALESCE(piss.IssuedToName, u.FullName, 'Unassigned') as accountable_officer"),
                'pid.deleted_at as removed_at',
            ])
            ->forPage($page, $perPage)
            ->get();

        return response()->json([
            'data'         => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ]);

    } catch (\Throwable $e) {
        Log::error('InventoryRemovalController::removedItems error', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'sql'     => method_exists($e, 'getSql') ? $e->getSql() : null,
        ]);

        return response()->json([
            'message' => 'Failed to retrieve removed items',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
}
