<?php

namespace App\Http\Controllers;

use App\Models\AssignedProperty;
use App\Models\Auth\User;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Condition;
use App\Models\MasterData\PropertyType;
use App\Models\RpcInventoryAuditLog;
use App\Models\RpcInventoryCycle;
use App\Models\RpcInventoryScan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RpcInventoryController extends Controller
{
    private const ROLE_ADMIN = 'Admin';
    private const ROLE_GSO_STAFF = 'GSO Staff';

    private const CLASSIFICATION_SEMI_EXPENDABLE = 1;
    private const CLASSIFICATION_NON_EXPENDABLE = 2;

    /**
     * @var array<int, string|null>
     */
    private array $departmentOfficerNameCache = [];

    public function index(Request $request): JsonResponse
    {
        $this->authorizeGso();

        $query = RpcInventoryCycle::query()
            ->withCount('scans')
            ->withCount([
                'scans as rpcppe_count' => fn ($q) => $q->where('rpc_type', 'RPCPPE'),
                'scans as rpcsep_count' => fn ($q) => $q->where('rpc_type', 'RPCSEP'),
            ])
            ->orderByDesc('start_year');

        if ($request->filled('year')) {
            $year = (int) $request->query('year');
            if ($year >= 2000 && $year <= 9999) {
                $query->where(function ($q) use ($year) {
                    $q->where('start_year', $year)->orWhere('end_year', $year);
                });
            }
        }

        $today = Carbon::now();
        $currentStartYear = (int) $today->year;
        $currentEndYear = $currentStartYear; // use calendar year only
        $cycles = $query->get()->map(function ($c) {
            $arr = $c->toArray();
            $arr['rpcppe_label'] = sprintf('RPCPPE %d', (int) $c->start_year);
            $arr['rpcsep_label'] = sprintf('RPCSEP %d', (int) $c->start_year);
            return $arr;
        });

        return response()->json([
            'data' => $cycles,
            'current_cycle' => [
                'start_year' => $currentStartYear,
                'end_year' => $currentEndYear,
                'label' => $this->cycleLabel($currentStartYear, $currentEndYear),
            ],
        ]);
    }

    public function createCurrentCycle(): JsonResponse
    {
        $user = $this->authorizeGso();

        $startYear = (int) now()->year;
        $endYear = $startYear; // calendar year only
        $windowStart = Carbon::create($startYear, 9, 1, 0, 0, 0);

        if (now()->lt($windowStart)) {
            return response()->json([
                'message' => 'RPC inventory cycles may only be opened on or after September 1.',
                'available_at' => $windowStart->toIso8601String(),
                'year' => $startYear,
            ], 403);
        }

        $cycle = RpcInventoryCycle::withTrashed()
            ->where('start_year', $startYear)
            ->first();

        if ($cycle && $cycle->trashed()) {
            $cycle->restore();
            RpcInventoryAuditLog::createLog([
                'rpc_inventory_cycle_id' => (int) $cycle->id,
                'action' => 'cycle_restored',
                'source' => 'web',
                'performed_by_user_id' => (int) $user->UserID,
                'details' => [
                    'cycle_label' => $cycle->cycle_label,
                    'start_year' => (int) $cycle->start_year,
                    'end_year' => (int) $cycle->end_year,
                ],
            ]);

            return response()->json([
                'message' => 'Inventory cycle ready',
                'cycle' => $cycle,
            ], 201);
        }

        if (!$cycle) {
            // Ensure there is no active (non-trashed) cycle for this calendar year
            $existingActive = RpcInventoryCycle::where('start_year', $startYear)->first();
            if ($existingActive) {
                return response()->json([
                    'message' => 'An RPCPPE cycle for this calendar year already exists',
                    'year' => $startYear,
                ], 409);
            }

            $cycle = RpcInventoryCycle::create([
                'cycle_label' => $this->cycleLabel($startYear, $endYear),
                'start_year' => $startYear,
                'end_year' => $endYear,
                'status' => 'active',
                'started_at' => now(),
                'created_by_user_id' => (int) $user->UserID,
            ]);

            RpcInventoryAuditLog::createLog([
                'rpc_inventory_cycle_id' => (int) $cycle->id,
                'action' => 'cycle_created',
                'source' => 'web',
                'performed_by_user_id' => (int) $user->UserID,
                'details' => [
                    'cycle_label' => $cycle->cycle_label,
                    'start_year' => (int) $cycle->start_year,
                    'end_year' => (int) $cycle->end_year,
                    'status' => $cycle->status,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Inventory cycle ready',
            'cycle' => $cycle,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorizeGso();

        $cycle = RpcInventoryCycle::withCount('scans')
            ->withCount([
                'scans as rpcppe_count' => fn ($q) => $q->where('rpc_type', 'RPCPPE'),
                'scans as rpcsep_count' => fn ($q) => $q->where('rpc_type', 'RPCSEP'),
            ])
            ->findOrFail($id);

        $arr = $cycle->toArray();
        $arr['rpcppe_label'] = sprintf('RPCPPE %d', (int) $cycle->start_year);
        $arr['rpcsep_label'] = sprintf('RPCSEP %d', (int) $cycle->start_year);

        return response()->json($arr);
    }

    public function closureSummary(int $id): JsonResponse
    {
        $this->authorizeAdmin();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $scans = RpcInventoryScan::query()
            ->where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->with(['condition:ConditionID,ConditionName'])
            ->get();

        $scannedByDetailId = $scans->keyBy('inventory_detail_id');
        $lastScanTime = $scans->pluck('scanned_at')->filter()->max();

        $assigned = AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.drDetail:ArticleID,ItemDescription',
                'inventoryDetail.drDetail.deliveryReceipt:DRID,DateReceived',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->get();

        $inventoryDetails = PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'drDetail:ArticleID,ItemDescription',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->whereHas('propertyType', function ($query) {
                $query->whereIn('PropertyClassificationID', [
                    self::CLASSIFICATION_NON_EXPENDABLE,
                    self::CLASSIFICATION_SEMI_EXPENDABLE,
                ]);
            })
            ->get();

        $issuanceStatuses = DB::table('propertyissuancedetail')
            ->selectRaw('propertyissuancedetail.InventoryDetailID, propertyissuance.Status')
            ->join('propertyissuance', 'propertyissuance.IssuanceID', '=', 'propertyissuancedetail.IssuanceID')
            ->where('propertyissuance.deleted_at', null)
            ->where('propertyissuancedetail.deleted_at', null)
            ->get();

        $issuanceStatusMap = collect([]);
        foreach ($issuanceStatuses as $row) {
            $detailId = (int) $row->InventoryDetailID;
            if (!$issuanceStatusMap->has($detailId)) {
                $issuanceStatusMap->put($detailId, $row->Status);
            }
        }

        $mappedByInventoryDetail = [];

        foreach ($assigned as $property) {
            $mapped = $this->mapAssignedProperty($property);
            $rpcType = (string) $mapped['rpc_type'];
            $inventoryDetailId = (int) ($mapped['inventory_detail_id'] ?? 0);

            if (!in_array($rpcType, ['RPCPPE', 'RPCSEP'], true) || $inventoryDetailId <= 0) {
                continue;
            }

            $mappedByInventoryDetail[$inventoryDetailId] = $mapped;
        }

        foreach ($inventoryDetails as $detail) {
            $inventoryDetailId = (int) ($detail->InventoryDetailID ?? 0);
            if ($inventoryDetailId <= 0 || isset($mappedByInventoryDetail[$inventoryDetailId])) {
                continue;
            }

            $mapped = $this->mapInventoryDetail($detail);
            $rpcType = (string) $mapped['rpc_type'];
            if (!in_array($rpcType, ['RPCPPE', 'RPCSEP'], true)) {
                continue;
            }

            $mappedByInventoryDetail[$inventoryDetailId] = $mapped;
        }

        $expectedDetails = [];
        foreach ($mappedByInventoryDetail as $inventoryDetailId => $mapped) {
            $issuanceStatus = $issuanceStatusMap->get($inventoryDetailId);
            if ($issuanceStatus !== 'acknowledged') {
                continue;
            }
            $expectedDetails[$inventoryDetailId] = $mapped;
        }

        $totalExpected = count($expectedDetails);
        $scannedTotal = 0;
        $completedDepartments = collect([]);
        $expectedDepartments = collect([]);

        foreach ($expectedDetails as $inventoryDetailId => $mapped) {
            if ($scannedByDetailId->has($inventoryDetailId)) {
                $scannedTotal++;
                if (!empty($mapped['department_id'])) {
                    $completedDepartments->push((int) $mapped['department_id']);
                }
            }
            if (!empty($mapped['department_id'])) {
                $expectedDepartments->push((int) $mapped['department_id']);
            }
        }

        $completedDepartments = $completedDepartments->unique();
        $expectedDepartments = $expectedDepartments->unique();

        $completedLocations = $completedDepartments->count();
        $pendingLocations = max(0, $expectedDepartments->count() - $completedLocations);

        $serviceableCount = 0;
        $unserviceableCount = 0;
        foreach ($scans as $scan) {
            $cName = strtolower(trim($scan->condition?->ConditionName ?? ''));
            if (str_contains($cName, 'unserviceable')) {
                $unserviceableCount++;
            } elseif (str_contains($cName, 'serviceable')) {
                $serviceableCount++;
            }
        }

        $progressPercentage = $totalExpected > 0
            ? round(($scannedTotal / $totalExpected) * 100, 1)
            : ($scannedTotal > 0 ? 100 : 0);

        return response()->json([
            'total_expected' => $totalExpected,
            'total_scanned' => $scannedTotal,
            'total_scans_recorded' => $scans->count(),
            'missing_count' => max(0, $totalExpected - $scannedTotal),
            'excess_count' => max(0, $scans->count() - $scannedTotal),
            'serviceable_count' => $serviceableCount,
            'unserviceable_count' => $unserviceableCount,
            'completed_locations' => $completedLocations,
            'pending_locations' => $pendingLocations,
            'completed_departments' => $completedLocations,
            'pending_departments' => $pendingLocations,
            'total_departments' => $expectedDepartments->count(),
            'progress_percentage' => $progressPercentage,
            'last_scan_time' => $lastScanTime ? $lastScanTime->toIso8601String() : null,
            'reopen_count' => (int) ($cycle->reopen_count ?? 0),
            'max_reopens' => 2,
            'can_reopen' => $cycle->canReopen(),
        ]);
    }

    public function scans(Request $request, int $id): JsonResponse
    {
        $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $perPage = max(10, min(100, (int) $request->query('per_page', 25)));
        $query = RpcInventoryScan::query()
            ->where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->leftJoin('propertyinventorydetail as invd', 'invd.InventoryDetailID', '=', 'rpc_inventory_scans.inventory_detail_id')
            ->leftJoin('propertytype as pt', 'pt.PropertyTypeID', '=', 'invd.PropertyTypeID')
            ->leftJoin('department as dep', 'dep.DepartmentID', '=', 'rpc_inventory_scans.department_id')
            ->select('rpc_inventory_scans.*')
            ->selectRaw('pt.PropertyName as property_type')
            ->selectRaw('dep.DepartmentName as department_name')
            ->with(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName'])
            ->orderByDesc('scanned_at')
            ->orderByDesc('id');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('property_number', 'like', "%{$search}%")
                    ->orWhere('article', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('accountable_officer', 'like', "%{$search}%");
            });
        }

        $type = strtoupper((string) $request->query('type', ''));
        if (in_array($type, ['RPCPPE', 'RPCSEP'], true)) {
            $query->where('rpc_type', $type);
        }

        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(function (RpcInventoryScan $scan) {
            $departmentName = is_string($scan->department_name ?? null) ? trim((string) $scan->department_name) : '';
            $officerName = is_string($scan->accountable_officer) ? trim($scan->accountable_officer) : '';

            if ($officerName !== '' && $departmentName !== '') {
                $scan->accountable_officer = $officerName . ' - ' . $departmentName;
            } elseif ($departmentName !== '' && $officerName === '') {
                $scan->accountable_officer = $departmentName;
            }

            $scan->created_month = $scan->scanned_at?->timezone('Asia/Manila')?->format('F Y');

            return $scan;
        });

        return response()->json($paginator);
    }

    public function previewScan(Request $request, int $id): JsonResponse
    {
        $this->authorizeGso();
        
        $cycle = RpcInventoryCycle::find($id);
        if (!$cycle) {
            return response()->json([
                'message' => "Inventory cycle with ID {$id} not found. Please create or select a valid cycle.",
                'cycle_id' => $id,
            ], 404);
        }

        if ($cycle->isClosed()) {
            return response()->json([
                'message' => 'Cannot preview or scan in a closed inventory cycle. The cycle is locked and read-only.',
                'cycle_status' => $cycle->status,
                'closed_at' => $cycle->closed_at,
            ], 409);
        }

        $validated = $request->validate([
            'property_id' => 'nullable|integer|min:1',
            'inventory_detail_id' => 'nullable|integer|min:1',
            'property_number' => 'nullable|string|max:100',
            'qr_value' => 'nullable|string|max:2048',
        ]);

        $propertyId = isset($validated['property_id']) ? (int) $validated['property_id'] : null;
        $inventoryDetailId = isset($validated['inventory_detail_id']) ? (int) $validated['inventory_detail_id'] : null;
        $propertyNumber = isset($validated['property_number'])
            ? trim((string) $validated['property_number'])
            : null;

        \Log::debug('RPC inventory scan preview request', [
            'cycle_id' => $id,
            'property_id' => $propertyId,
            'inventory_detail_id' => $inventoryDetailId,
            'property_number' => $propertyNumber,
            'qr_value' => $validated['qr_value'] ?? null,
        ]);

        if (!$propertyId && !empty($validated['qr_value'])) {
            $propertyId = $this->extractPropertyId((string) $validated['qr_value']);
            if (!$propertyId && !$propertyNumber) {
                $propertyNumber = trim((string) $validated['qr_value']);
            }
        }

        if (!$propertyId && !$propertyNumber && !$inventoryDetailId) {
            return response()->json([
                'message' => 'Unable to determine property from scan input. Provide property ID, inventory detail ID, property number, or QR value.',
                'received' => $validated,
            ], 422);
        }

        $mapped = null;

        if ($propertyId) {
            $assignedProperty = $this->resolveAssignedProperty($propertyId);
            if ($assignedProperty) {
                $mapped = $this->mapAssignedProperty($assignedProperty);
            } else {
                $inventoryDetail = $this->resolveInventoryDetailById($propertyId);
                if ($inventoryDetail) {
                    $mapped = $this->mapInventoryDetail($inventoryDetail);
                }
            }
        } elseif ($inventoryDetailId) {
            $inventoryDetail = $this->resolveInventoryDetailById($inventoryDetailId);
            if ($inventoryDetail) {
                $mapped = $this->mapInventoryDetail($inventoryDetail);
            }
        } elseif ($propertyNumber) {
            $assignedProperty = $this->resolveAssignedPropertyByPropertyNumber((string) $propertyNumber);
            if ($assignedProperty) {
                $mapped = $this->mapAssignedProperty($assignedProperty);
            } else {
                $inventoryDetail = $this->resolveInventoryDetailByPropertyNumber((string) $propertyNumber);
                if ($inventoryDetail) {
                    $mapped = $this->mapInventoryDetail($inventoryDetail);
                }
            }
        }

        if (!$mapped) {
            $debug = [
                'input_property_id' => $propertyId,
                'input_inventory_detail_id' => $inventoryDetailId,
                'input_property_number' => $propertyNumber,
                'qr_value' => $validated['qr_value'] ?? null,
            ];
            
            \Log::warning('RPC inventory scan preview: property not found', $debug);
            
            return response()->json([
                'message' => 'Property not found for the provided input. Verify the scanned QR code, property ID, or property number exists.',
                'input' => $debug,
            ], 404);
        }

        // For one-asset-per-scan requirement, only return the matched asset
        $detailId = (int) ($mapped['inventory_detail_id'] ?? 0);
        $existingScan = null;
        if ($detailId > 0) {
            $existingScan = RpcInventoryScan::query()
                ->where('rpc_inventory_cycle_id', (int) $cycle->id)
                ->where('inventory_detail_id', $detailId)
                ->first();
        }

        $candidate = [
            'inventory_detail_id' => $detailId,
            'property_id' => isset($mapped['property_id']) && $mapped['property_id'] !== null ? (int) $mapped['property_id'] : null,
            'property_number' => $mapped['property_number'] ?? null,
            'is_scanned' => (bool) $existingScan,
            'existing_scan_id' => $existingScan ? (int) $existingScan->id : null,
        ];

        return response()->json([
            'asset' => $mapped,
            'already_scanned' => (bool) $existingScan,
            'existing_scan_id' => $existingScan ? (int) $existingScan->id : null,
            'group_total_count' => 1,
            'group_scanned_count' => $existingScan ? 1 : 0,
            'group_remaining_count' => $existingScan ? 0 : 1,
            'candidates' => [$candidate],
        ]);
    }

    /**
     * Update asset condition directly from scan preview or manual action.
     * Connects directly to PropertyInventoryDetail and AssignedProperty (Inventory / All-Items).
     */
    public function updateAssetCondition(Request $request, ?int $id = null): JsonResponse
    {
        $user = $this->authorizeGso();

        $validated = $request->validate([
            'inventory_detail_id' => 'nullable|integer|min:1',
            'property_id' => 'nullable|integer|min:1',
            'condition_id' => 'required|integer|exists:condition,ConditionID',
            'cycle_id' => 'nullable|integer|min:1',
        ]);

        $inventoryDetailId = isset($validated['inventory_detail_id']) ? (int) $validated['inventory_detail_id'] : 0;
        $propertyId = isset($validated['property_id']) ? (int) $validated['property_id'] : null;
        $conditionId = (int) $validated['condition_id'];

        if ($inventoryDetailId <= 0 && (!$propertyId || $propertyId <= 0)) {
            return response()->json(['message' => 'Property ID or Inventory Detail ID is required.'], 422);
        }

        if ($inventoryDetailId <= 0 && $propertyId > 0) {
            $prop = AssignedProperty::find($propertyId);
            if ($prop && $prop->InventoryDetailID) {
                $inventoryDetailId = (int) $prop->InventoryDetailID;
            }
        }

        $this->syncInventoryCondition($inventoryDetailId, $propertyId, $conditionId);

        $cycleId = $id ? (int) $id : (int) ($validated['cycle_id'] ?? 0);
        if ($cycleId > 0 && $inventoryDetailId > 0) {
            RpcInventoryScan::where('rpc_inventory_cycle_id', $cycleId)
                ->where('inventory_detail_id', $inventoryDetailId)
                ->update(['condition_id' => $conditionId]);
        }

        $condition = Condition::find($conditionId);

        if ($cycleId > 0) {
            $propNum = null;
            $art = null;
            $desc = null;
            $officer = null;
            $rpcType = null;

            if ($inventoryDetailId > 0) {
                $invd = $this->resolveInventoryDetailById($inventoryDetailId);
                if ($invd) {
                    $mapped = $this->mapInventoryDetail($invd);
                    $propNum = $mapped['property_number'] ?? null;
                    $art = $mapped['article'] ?? null;
                    $desc = $mapped['description'] ?? null;
                    $officer = $mapped['accountable_officer'] ?? null;
                    $rpcType = $mapped['rpc_type'] ?? null;
                }
            } elseif ($propertyId > 0) {
                $assigned = $this->resolveAssignedProperty($propertyId);
                if ($assigned) {
                    $mapped = $this->mapAssignedProperty($assigned);
                    $propNum = $mapped['property_number'] ?? null;
                    $art = $mapped['article'] ?? null;
                    $desc = $mapped['description'] ?? null;
                    $officer = $mapped['accountable_officer'] ?? null;
                    $rpcType = $mapped['rpc_type'] ?? null;
                }
            }

            $this->createAuditEntry(
                $cycleId,
                'asset_condition_updated',
                $user,
                [
                    'inventory_detail_id' => $inventoryDetailId,
                    'property_id' => $propertyId,
                    'property_number' => $propNum,
                    'article' => $art,
                    'description' => $desc,
                    'accountable_officer' => $officer,
                    'rpc_type' => $rpcType,
                    'condition_id' => $conditionId,
                    'condition_name' => $condition?->ConditionName,
                    'source' => 'preview_scan',
                ],
                null,
                $conditionId
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Item condition updated to {$condition?->ConditionName} in inventory and all items.",
            'inventory_detail_id' => $inventoryDetailId,
            'property_id' => $propertyId,
            'condition_id' => $conditionId,
            'condition_name' => $condition?->ConditionName,
        ]);
    }

    /**
     * Synchronize condition with PropertyInventoryDetail and AssignedProperty (Inventory / All-Items)
     */
    private function syncInventoryCondition(int $inventoryDetailId, ?int $propertyId, int $conditionId): void
    {
        if ($inventoryDetailId > 0) {
            PropertyInventoryDetail::where('InventoryDetailID', $inventoryDetailId)
                ->update(['ConditionID' => $conditionId]);

            AssignedProperty::where('InventoryDetailID', $inventoryDetailId)
                ->update(['ConditionID' => $conditionId]);
        }

        if ($propertyId !== null && $propertyId > 0) {
            AssignedProperty::where('PropertyID', $propertyId)
                ->update(['ConditionID' => $conditionId]);

            $assigned = AssignedProperty::find($propertyId);
            if ($assigned && $assigned->InventoryDetailID) {
                PropertyInventoryDetail::where('InventoryDetailID', (int) $assigned->InventoryDetailID)
                    ->update(['ConditionID' => $conditionId]);
            }
        }
    }

    public function storeScan(Request $request, int $id): JsonResponse
    {
        $user = $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        // Check if cycle is closed
        if ($cycle->isClosed()) {
            return response()->json([
                'message' => 'Cannot add scans to a closed inventory cycle. The cycle is locked and read-only.',
                'cycle_status' => $cycle->status,
                'closed_at' => $cycle->closed_at,
            ], 409);
        }

        $validated = $request->validate([
            'property_id' => 'nullable|integer|min:1',
            'inventory_detail_id' => 'nullable|integer|min:1',
            'condition_id' => 'required|integer|exists:condition,ConditionID',
            'quantity_per_count' => 'nullable|integer|in:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $propertyId = isset($validated['property_id']) ? (int) $validated['property_id'] : null;
        $inventoryDetailId = isset($validated['inventory_detail_id']) ? (int) $validated['inventory_detail_id'] : null;

        if (!$propertyId && !$inventoryDetailId) {
            return response()->json(['message' => 'Property ID or Inventory Detail ID is required'], 422);
        }

        $mapped = null;

        if ($propertyId) {
            $assignedProperty = $this->resolveAssignedProperty($propertyId);
            if (!$assignedProperty) {
                return response()->json(['message' => 'Property not found'], 404);
            }
            $mapped = $this->mapAssignedProperty($assignedProperty);
        } elseif ($inventoryDetailId) {
            $inventoryDetail = $this->resolveInventoryDetailById($inventoryDetailId);
            if (!$inventoryDetail) {
                return response()->json(['message' => 'Inventory item not found'], 404);
            }
            $mapped = $this->mapInventoryDetail($inventoryDetail);
        }

        $groupContext = $this->resolveScanGroupContext($mapped, (int) $cycle->id);
        $mappedByDetail = $groupContext['mapped_by_detail'] ?? [];
        $existingScansByDetail = $groupContext['existing_scans_by_detail'] ?? [];

        // Enforce one-asset-per-scan: ignore any multi-select and only use the mapped inventory detail
        $requestedDetailIds = [(int) $mapped['inventory_detail_id']];
        $createdScans = [];
        $isBulk = false;

        try {
            DB::transaction(function () use (
                &$createdScans,
                $cycle,
                $requestedDetailIds,
                $mappedByDetail,
                $existingScansByDetail,
                $validated,
                $user,
                $isBulk
            ): void {
                foreach ($requestedDetailIds as $detailId) {
                    $row = $mappedByDetail[$detailId];

                    $scan = RpcInventoryScan::create([
                        'rpc_inventory_cycle_id' => (int) $cycle->id,
                        'inventory_detail_id' => (int) $row['inventory_detail_id'],
                        'property_id' => isset($row['property_id']) && $row['property_id'] !== null ? (int) $row['property_id'] : null,
                        'rpc_type' => (string) $row['rpc_type'],
                        'property_classification_id' => isset($row['property_classification_id']) && $row['property_classification_id'] !== null
                            ? (int) $row['property_classification_id']
                            : null,
                        'condition_id' => (int) $validated['condition_id'],
                        'scanned_at' => now(),
                        'scanned_by_user_id' => (int) $user->UserID,
                        'end_user_id' => isset($row['end_user_id']) && $row['end_user_id'] !== null ? (int) $row['end_user_id'] : null,
                        'department_id' => isset($row['department_id']) && $row['department_id'] !== null ? (int) $row['department_id'] : null,
                        'article' => $row['article'] ?? null,
                        'description' => $row['description'] ?? null,
                        'property_number' => $row['property_number'] ?? null,
                        'unit_of_measure' => $row['unit_of_measure'] ?? null,
                        'unit_value' => $row['unit_value'] ?? null,
                        'quantity_per_card' => 1,
                        'quantity_per_count' => 1,
                        'remarks' => $validated['remarks'] ?? null,
                        'accountable_officer' => $row['accountable_officer'] ?? null,
                    ]);

                    // Sync condition to PropertyInventoryDetail and AssignedProperty (Inventory / All Items)
                    $this->syncInventoryCondition(
                        (int) $row['inventory_detail_id'],
                        isset($row['property_id']) && $row['property_id'] !== null ? (int) $row['property_id'] : null,
                        (int) $validated['condition_id']
                    );

                    $createdScans[] = $scan;

                    if (! $isBulk) {
                        $this->createAuditEntry(
                            (int) $cycle->id,
                            'scan_created',
                            $user,
                            [
                                'rpc_type' => $row['rpc_type'] ?? null,
                                'article' => $row['article'] ?? null,
                                'description' => $row['description'] ?? null,
                                'accountable_officer' => $row['accountable_officer'] ?? null,
                                'property_number' => $row['property_number'] ?? null,
                                'property_id' => $row['property_id'] ?? null,
                                'inventory_detail_id' => $row['inventory_detail_id'] ?? null,
                                'condition_id' => (int) $validated['condition_id'],
                                'remarks' => $validated['remarks'] ?? null,
                            ],
                            $scan,
                            (int) $validated['condition_id']
                        );
                    }
                }

                // Propagate remarks to any existing sibling scans in this group.
                if (!empty($existingScansByDetail) && array_key_exists('remarks', $validated) && $validated['remarks'] !== null) {
                    foreach ($existingScansByDetail as $existingDetailId => $existingScan) {
                        // Skip scans we just created for the requested detail(s)
                        if (in_array((int) $existingDetailId, $requestedDetailIds, true)) {
                            continue;
                        }

                        $previousRemarks = $existingScan->remarks ?? null;
                        $newRemarks = $validated['remarks'];
                        if ($previousRemarks === $newRemarks) {
                            continue;
                        }

                        $existingScan->remarks = $newRemarks;
                        $existingScan->save();

                        // Record an audit entry for the update
                        $this->createAuditEntry(
                            (int) $cycle->id,
                            'scan_updated',
                            $user,
                            [
                                'inventory_detail_id' => $existingScan->inventory_detail_id,
                                'property_number' => $existingScan->property_number,
                            ],
                            $existingScan,
                            (int) $existingScan->condition_id,
                            null,
                            $previousRemarks,
                            $newRemarks
                        );
                    }
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                return response()->json([
                    'message' => 'One or more selected property numbers are already scanned in this cycle.',
                ], 409);
            }

            throw $e;
        }

        // No bulk audit entries — each scan has its own audit entry (one-scan → one-asset)

        return response()->json([
            'message' => count($createdScans) > 1
                ? sprintf('%d asset scans recorded.', count($createdScans))
                : 'Asset scan recorded.',
            'scans' => collect($createdScans)->map(
                fn (RpcInventoryScan $scan) => $scan->load(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName'])
            )->values(),
        ], 201);
    }

    public function updateScan(Request $request, int $scanId): JsonResponse
    {
        $user = $this->authorizeGso();

        $scan = RpcInventoryScan::with(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName'])
            ->findOrFail($scanId);
        
        // Check if cycle is closed
        $cycle = RpcInventoryCycle::findOrFail((int) $scan->rpc_inventory_cycle_id);
        if ($cycle->isClosed()) {
            return response()->json([
                'message' => 'Cannot modify scans in a closed inventory cycle. The cycle is locked and read-only.',
                'cycle_status' => $cycle->status,
                'closed_at' => $cycle->closed_at,
            ], 409);
        }

        $cycleId = (int) $scan->rpc_inventory_cycle_id;
        $previousConditionId = (int) $scan->condition_id;
        $previousRemarks = $scan->remarks;

        $validated = $request->validate([
            'condition_id' => 'required|integer|exists:condition,ConditionID',
            'quantity_per_count' => 'nullable|integer|in:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $newConditionId = (int) $validated['condition_id'];
        $newRemarks = array_key_exists('remarks', $validated) ? $validated['remarks'] : $scan->remarks;
        $hasChanges = $previousConditionId !== $newConditionId
            || (($previousRemarks ?? null) !== ($newRemarks ?? null))
            || (int) $scan->quantity_per_count !== 1;

        if (!$hasChanges) {
            return response()->json([
                'message' => 'No changes detected.',
                'scan' => $scan->fresh(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName']),
            ]);
        }

        DB::transaction(function () use (
            $scan,
            $validated,
            $cycleId,
            $user,
            $previousConditionId,
            $previousRemarks
        ): void {
            $scan->condition_id = (int) $validated['condition_id'];
            $scan->quantity_per_count = 1;
            if (array_key_exists('remarks', $validated)) {
                $scan->remarks = $validated['remarks'];
            }
            $scan->save();

            // Sync condition to PropertyInventoryDetail and AssignedProperty (Inventory / All Items)
            $this->syncInventoryCondition(
                (int) $scan->inventory_detail_id,
                $scan->property_id ? (int) $scan->property_id : null,
                (int) $validated['condition_id']
            );

            $this->createAuditEntry(
                $cycleId,
                'scan_updated',
                $user,
                [
                    'before' => [
                        'condition_id' => $previousConditionId,
                        'remarks' => $previousRemarks,
                    ],
                    'after' => [
                        'condition_id' => (int) $scan->condition_id,
                        'remarks' => $scan->remarks,
                    ],
                ],
                $scan,
                (int) $scan->condition_id,
                $previousConditionId,
                $previousRemarks,
                $scan->remarks
            );
        });

        return response()->json([
            'message' => 'Scanned asset updated.',
            'scan' => $scan->fresh(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName']),
        ]);
    }

    public function destroyScan(int $scanId): JsonResponse
    {
        $user = $this->authorizeGso();

        $scan = RpcInventoryScan::findOrFail($scanId);
        
        // Check if cycle is closed
        $cycle = RpcInventoryCycle::findOrFail((int) $scan->rpc_inventory_cycle_id);
        if ($cycle->isClosed()) {
            return response()->json([
                'message' => 'Cannot delete scans from a closed inventory cycle. The cycle is locked and read-only.',
                'cycle_status' => $cycle->status,
                'closed_at' => $cycle->closed_at,
            ], 409);
        }

        $cycleId = (int) $scan->rpc_inventory_cycle_id;
        $snapshot = [
            'property_number' => $scan->property_number,
            'article' => $scan->article,
            'description' => $scan->description,
            'accountable_officer' => $scan->accountable_officer,
            'property_id' => $scan->property_id,
            'inventory_detail_id' => $scan->inventory_detail_id,
            'condition_id' => $scan->condition_id,
            'remarks' => $scan->remarks,
            'rpc_type' => $scan->rpc_type,
            'scanned_at' => optional($scan->scanned_at)->toIso8601String(),
        ];

        DB::transaction(function () use ($scan, $cycleId, $snapshot, $user): void {
            $scan->delete();

            $this->createAuditEntry(
                $cycleId,
                'scan_deleted',
                $user,
                $snapshot,
                null,
                (int) ($snapshot['condition_id'] ?? 0) ?: null,
                null,
                $snapshot['remarks'] ?? null,
                null
            );
        });

        return response()->json(['message' => 'Scanned asset removed.']);
    }

    public function report(int $id): JsonResponse
    {
        $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $scans = RpcInventoryScan::query()
            ->where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->with(['condition:ConditionID,ConditionName'])
            ->get();

        $assigned = AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->get();

        $inventoryDetails = PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'drDetail.deliveryReceipt:DRID,DateReceived',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->whereHas('propertyType', function ($query) {
                $query->whereIn('PropertyClassificationID', [
                    self::CLASSIFICATION_NON_EXPENDABLE,
                    self::CLASSIFICATION_SEMI_EXPENDABLE,
                ]);
            })
            ->get();

        // Build a map of inventory_detail_id -> latest issuance status
        $issuanceStatuses = \DB::table('propertyissuancedetail')
            ->selectRaw('propertyissuancedetail.InventoryDetailID, propertyissuance.Status')
            ->join('propertyissuance', 'propertyissuance.IssuanceID', '=', 'propertyissuancedetail.IssuanceID')
            ->where('propertyissuance.deleted_at', null)
            ->where('propertyissuancedetail.deleted_at', null)
            ->get();
        
        $issuanceStatusMap = \collect([]);
        foreach ($issuanceStatuses as $row) {
            $detailId = (int) $row->InventoryDetailID;
            // Keep the first (latest) status found per inventory detail
            if (!$issuanceStatusMap->has($detailId)) {
                $issuanceStatusMap->put($detailId, $row->Status);
            }
        }

        // Get all removed items (marked as Lost/Stolen/Damaged/Destroyed in RLSDDP)
        $removedInventoryDetailIds = \DB::table('property_rlsddp_items')
            ->pluck('InventoryDetailID')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $scannedByDetailId = $scans->keyBy('inventory_detail_id');

        $rowsByType = [
            'RPCPPE' => [],
            'RPCSEP' => [],
        ];

        $totalsByType = [
            'RPCPPE' => ['scanned' => 0, 'total' => 0],
            'RPCSEP' => ['scanned' => 0, 'total' => 0],
        ];

        $mappedByInventoryDetail = [];

        foreach ($assigned as $property) {
            $mapped = $this->mapAssignedProperty($property);
            $rpcType = (string) $mapped['rpc_type'];
            $inventoryDetailId = (int) ($mapped['inventory_detail_id'] ?? 0);

            if (!in_array($rpcType, ['RPCPPE', 'RPCSEP'], true) || $inventoryDetailId <= 0) {
                continue;
            }

            $mappedByInventoryDetail[$inventoryDetailId] = $mapped;
        }

        foreach ($inventoryDetails as $detail) {
            $inventoryDetailId = (int) ($detail->InventoryDetailID ?? 0);
            if ($inventoryDetailId <= 0 || isset($mappedByInventoryDetail[$inventoryDetailId])) {
                continue;
            }

            $mapped = $this->mapInventoryDetail($detail);
            $rpcType = (string) $mapped['rpc_type'];
            if (!in_array($rpcType, ['RPCPPE', 'RPCSEP'], true)) {
                continue;
            }

            $mappedByInventoryDetail[$inventoryDetailId] = $mapped;
        }

        foreach ($mappedByInventoryDetail as $inventoryDetailId => $mapped) {
            $rpcType = (string) $mapped['rpc_type'];

            // Filter by issuance status: only include items that got acknowledged
            $issuanceStatus = $issuanceStatusMap->get($inventoryDetailId);
            if ($issuanceStatus !== 'acknowledged') {
                continue;
            }

            // Exclude items marked as removed (Lost/Stolen/Damaged/Destroyed in RLSDDP)
            if (in_array($inventoryDetailId, $removedInventoryDetailIds, true)) {
                continue;
            }

            $scan = $scannedByDetailId->get((int) $mapped['inventory_detail_id']);
            $quantityCard = 1;
            $quantityCount = $scan ? (int) $scan->quantity_per_count : 0;
            $shortageOverageQty = $quantityCount - $quantityCard;

            $totalsByType[$rpcType]['total']++;
            if ($scan) {
                $totalsByType[$rpcType]['scanned']++;
            }

            $rowsByType[$rpcType][] = [
                'property_type' => $mapped['property_type_name'] ?? '-',
                'article' => $mapped['article'] ?? '-',
                'description' => $mapped['description'] ?? '-',
                'property_number' => $mapped['property_number'] ?? '-',
                'unit_of_measure' => $mapped['unit_of_measure'] ?? '-',
                'unit_value' => $mapped['unit_value'] !== null ? (float) $mapped['unit_value'] : 0.0,
                'quantity_per_card' => $quantityCard,
                'quantity_per_count' => $quantityCount,
                'shortage_overage_qty' => $shortageOverageQty,
                'shortage_overage_value' => round($shortageOverageQty * (float) ($mapped['unit_value'] ?? 0), 2),
                // Do not inject "not found" placeholders here. Leave remarks null when
                // no scan exists so grouped property-number ranges remain a display-only
                // value and do not expand into per-number "not found" entries.
                'remarks' => $scan?->remarks ?? null,
                'accountable_officer' => $mapped['accountable_officer'] ?? '-',
                'date_purchased' => $mapped['date_purchased'] ? (string) $mapped['date_purchased'] : '-',
                'condition' => $scan?->condition?->ConditionName,
                'is_scanned' => (bool) $scan,
            ];
        }

        foreach (['RPCPPE', 'RPCSEP'] as $type) {
            $rowsByType[$type] = $this->aggregateRpcRows($rowsByType[$type]);

            usort($rowsByType[$type], static function (array $a, array $b) {
                $byType = strcmp((string) ($a['property_type'] ?? ''), (string) ($b['property_type'] ?? ''));
                if ($byType !== 0) {
                    return $byType;
                }

                $byArticle = strcmp((string) ($a['article'] ?? ''), (string) ($b['article'] ?? ''));
                if ($byArticle !== 0) {
                    return $byArticle;
                }

                return strcmp((string) $a['property_number'], (string) $b['property_number']);
            });
        }

        $propertyTypesByRpcType = [
            'RPCPPE' => PropertyType::query()
                ->where('PropertyClassificationID', self::CLASSIFICATION_NON_EXPENDABLE)
                ->orderBy('PropertyName')
                ->pluck('PropertyName')
                ->map(static fn ($name) => (string) $name)
                ->values()
                ->all(),
            'RPCSEP' => PropertyType::query()
                ->where('PropertyClassificationID', self::CLASSIFICATION_SEMI_EXPENDABLE)
                ->orderBy('PropertyName')
                ->pluck('PropertyName')
                ->map(static fn ($name) => (string) $name)
                ->values()
                ->all(),
        ];

        return response()->json([
            'cycle' => $cycle,
            'generated_at' => now()->toIso8601String(),
            'rpcppe' => [
                'rows' => $rowsByType['RPCPPE'],
                'property_types' => $propertyTypesByRpcType['RPCPPE'],
                'scanned_count' => $totalsByType['RPCPPE']['scanned'],
                'total_count' => $totalsByType['RPCPPE']['total'],
            ],
            'rpcsep' => [
                'rows' => $rowsByType['RPCSEP'],
                'property_types' => $propertyTypesByRpcType['RPCSEP'],
                'scanned_count' => $totalsByType['RPCSEP']['scanned'],
                'total_count' => $totalsByType['RPCSEP']['total'],
            ],
        ]);
    }

    public function conditions(): JsonResponse
    {
        $this->authorizeGso();

        $conditions = Condition::query()
            ->orderBy('ConditionName')
            ->get(['ConditionID', 'ConditionName'])
            ->map(static function ($condition) {
                return [
                    'ConditionID' => (int) $condition->ConditionID,
                    'ConditionName' => (string) $condition->ConditionName,
                ];
            })
            ->values();

        return response()->json(['data' => $conditions]);
    }

    public function departmentCrossCheck(Request $request): JsonResponse
    {
        $this->authorizeGso();

        $departmentId = $request->query('department_id');
        if (!$departmentId) {
            return response()->json([
                'message' => 'Department is required for cross-checking.',
                'data' => [],
            ], 422);
        }

        $department = DB::table('department')
            ->where('DepartmentID', (int) $departmentId)
            ->first(['DepartmentID', 'DepartmentName', 'DepartmentCode']);

        if (!$department) {
            return response()->json([
                'message' => 'Department not found.',
                'data' => [],
            ], 404);
        }

        // Resolve Department Head
        $departmentHead = DB::table('user as u')
            ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
            ->where('u.DepartmentID', (int) $departmentId)
            ->where('u.IsActive', 1)
            ->where('ur.RoleName', 'Department Head')
            ->orderByDesc('u.UpdatedDate')
            ->orderByDesc('u.CreatedDate')
            ->value('u.FullName');

        $typeFilter = strtoupper(trim((string) $request->query('type', 'ALL')));
        $search = trim((string) $request->query('search', ''));

        // Exclude removed items (marked as Lost/Stolen/Damaged/Destroyed in RLSDDP)
        $removedDetailIds = DB::table('property_rlsddp_items')
            ->pluck('InventoryDetailID')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $query = DB::table('assignedproperty as ap')
            ->join('propertyinventorydetail as pid', 'pid.InventoryDetailID', '=', 'ap.InventoryDetailID')
            ->join('propertytype as pt', 'pt.PropertyTypeID', '=', 'pid.PropertyTypeID')
            ->leftJoin('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'pid.SourceDRDetailID')
            ->leftJoin('deliveryreceipt as dr', 'dr.DRID', '=', 'drd.DRID')
            ->leftJoin('supplier as s', 's.SupplierID', '=', 'dr.SupplierID')
            ->leftJoin('articles as a', 'a.ArticleID', '=', 'drd.ArticleID')
            ->leftJoin('unitofmeasure as uom', 'uom.UnitOfMeasureID', '=', 'pid.UnitOfMeasureID')
            ->leftJoin('enduser as eu', 'eu.EndUserID', '=', 'ap.EndUserID')
            ->where('ap.DepartmentID', (int) $departmentId)
            ->whereNull('pid.deleted_at');

        if (!empty($removedDetailIds)) {
            $query->whereNotIn('pid.InventoryDetailID', $removedDetailIds);
        }

        // Classification filter: 1 = Semi-Expendable (RPCSEP), 2 = Non-Expendable (RPCPPE)
        if ($typeFilter === 'RPCPPE') {
            $query->where('pt.PropertyClassificationID', self::CLASSIFICATION_NON_EXPENDABLE);
        } elseif ($typeFilter === 'RPCSEP') {
            $query->where('pt.PropertyClassificationID', self::CLASSIFICATION_SEMI_EXPENDABLE);
        } else {
            $query->whereIn('pt.PropertyClassificationID', [
                self::CLASSIFICATION_NON_EXPENDABLE,
                self::CLASSIFICATION_SEMI_EXPENDABLE,
            ]);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pid.PropertyNumber', 'like', "%{$search}%")
                    ->orWhere('dr.PONumber', 'like', "%{$search}%")
                    ->orWhere('s.SupplierName', 'like', "%{$search}%")
                    ->orWhere('a.ArticleName', 'like', "%{$search}%")
                    ->orWhere('pid.Brand', 'like', "%{$search}%")
                    ->orWhere('pid.ModelNumber', 'like', "%{$search}%")
                    ->orWhere('pid.SerialNumber', 'like', "%{$search}%");
            });
        }

        $query->addSelect([
            'pid.InventoryDetailID',
            'pid.PropertyNumber',
            'dr.DRID',
            'dr.DRNumber',
            'dr.PONumber',
            's.SupplierName',
            'pid.Quantity',
            'uom.UnitName as Unit',
            'a.ArticleName',
            'drd.ItemDescription as DrItemDescription',
            'pid.Description as InventoryDescription',
            'pid.Brand',
            'pid.ModelNumber',
            'pid.SerialNumber',
            'pid.UnitCost',
            'pid.TotalCost',
            'dr.DateReceived',
            'pid.Remarks as InventoryRemarks',
            'pt.PropertyClassificationID',
            'pt.PropertyName as PropertyTypeName',
            'eu.FullName as FallbackEndUserName',
        ]);

        $query->addSelect(DB::raw("(
            SELECT pi.DocumentNumber
            FROM propertyissuancedetail pidet
            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
            WHERE pidet.InventoryDetailID = pid.InventoryDetailID
                AND pi.deleted_at IS NULL
                AND pidet.deleted_at IS NULL
                AND pi.Status = 'acknowledged'
            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
            LIMIT 1
        ) as ParIcsNumber"));

        $query->addSelect(DB::raw("(
            SELECT COALESCE(pidet.Remarks, pi.Remarks)
            FROM propertyissuancedetail pidet
            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
            WHERE pidet.InventoryDetailID = pid.InventoryDetailID
                AND pi.deleted_at IS NULL
                AND pidet.deleted_at IS NULL
                AND pi.Status = 'acknowledged'
            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
            LIMIT 1
        ) as IssuanceRemarks"));

        $query->addSelect(DB::raw("(
            SELECT pia.receiver_name
            FROM propertyissuancedetail pidet2
            JOIN propertyissuance pi2 ON pi2.IssuanceID = pidet2.IssuanceID
            LEFT JOIN property_issuance_assignments pia ON pia.issuance_detail_id = pidet2.IssuanceDetailID
            WHERE pidet2.InventoryDetailID = pid.InventoryDetailID
                AND pi2.deleted_at IS NULL
                AND pidet2.deleted_at IS NULL
                AND pia.receiver_name IS NOT NULL
                AND pia.receiver_name != ''
            ORDER BY pia.id DESC, pi2.DateIssued DESC, pi2.IssuanceID DESC
            LIMIT 1
        ) as AssignedReceiverName"));

        $rawRows = $query->orderBy('pid.PropertyNumber')->get();

        // Get recorded scans for the cycle (or latest cycle)
        $cycleId = $request->query('cycle_id');
        $scansQuery = DB::table('rpc_inventory_scans');
        if ($cycleId) {
            $scansQuery->where('rpc_inventory_cycle_id', (int) $cycleId);
        } else {
            $latestCycle = DB::table('rpc_inventory_cycles')->orderByDesc('start_year')->first();
            if ($latestCycle) {
                $scansQuery->where('rpc_inventory_cycle_id', (int) $latestCycle->id);
            }
        }
        $scannedDetailIds = $scansQuery->pluck('inventory_detail_id')->map(fn ($id) => (int) $id)->flip()->toArray();

        // 1. Group by DR + Article + Description + Model + Brand to calculate group scan stats
        $groupStats = [];
        foreach ($rawRows as $row) {
            $drKey = $row->DRNumber ? trim((string) $row->DRNumber) : ($row->PONumber ? trim((string) $row->PONumber) : 'DR-' . (int) ($row->DRID ?? 0));
            $article = strtolower(trim((string) ($row->ArticleName ?? '')));
            $desc = strtolower(trim((string) ($row->DrItemDescription ?: $row->InventoryDescription)));
            $brand = strtolower(trim((string) ($row->Brand ?? '')));
            $model = strtolower(trim((string) ($row->ModelNumber ?? '')));

            $groupKey = implode('||', [$drKey, $article, $desc, $brand, $model]);
            $isScanned = isset($scannedDetailIds[(int) $row->InventoryDetailID]);

            if (!isset($groupStats[$groupKey])) {
                $groupStats[$groupKey] = ['total' => 0, 'scanned' => 0];
            }
            $groupStats[$groupKey]['total']++;
            if ($isScanned) {
                $groupStats[$groupKey]['scanned']++;
            }
        }

        // 2. Separate rows if they have different serial numbers; only combine if they have the same DR, article, description, model, brand.
        // If an item has a serial number, each unique serial number stays on its own row.
        $combinedBuckets = [];
        foreach ($rawRows as $row) {
            $drKey = $row->DRNumber ? trim((string) $row->DRNumber) : ($row->PONumber ? trim((string) $row->PONumber) : 'DR-' . (int) ($row->DRID ?? 0));
            $article = strtolower(trim((string) ($row->ArticleName ?? '')));
            $desc = strtolower(trim((string) ($row->DrItemDescription ?: $row->InventoryDescription)));
            $brand = strtolower(trim((string) ($row->Brand ?? '')));
            $model = strtolower(trim((string) ($row->ModelNumber ?? '')));
            $serial = trim((string) ($row->SerialNumber ?? ''));

            $groupKey = implode('||', [$drKey, $article, $desc, $brand, $model]);

            if ($serial !== '') {
                // Different serial number -> separate row!
                $bucketKey = $groupKey . '||SN:' . strtolower($serial) . '||PID:' . $row->InventoryDetailID;
            } else {
                // Same DR, article, description, model, brand with no serial number -> combine!
                $bucketKey = $groupKey . '||NO_SN';
            }

            if (!isset($combinedBuckets[$bucketKey])) {
                $combinedBuckets[$bucketKey] = [
                    'groupKey' => $groupKey,
                    'row' => $row,
                    'propertyNumbers' => [],
                    'quantity' => 0,
                    'inventoryDetailIds' => [],
                ];
            }

            $combinedBuckets[$bucketKey]['propertyNumbers'][] = trim((string) $row->PropertyNumber);
            $combinedBuckets[$bucketKey]['quantity'] += (int) ($row->Quantity ?? 1);
            $combinedBuckets[$bucketKey]['inventoryDetailIds'][] = (int) $row->InventoryDetailID;
        }

        $items = [];
        $totalCost = 0.0;
        $rpcppeCount = 0;
        $rpcsepCount = 0;

        foreach ($combinedBuckets as $bucket) {
            $row = $bucket['row'];
            $stats = $groupStats[$bucket['groupKey']];
            $totalInGroup = $stats['total'];
            $scannedInGroup = $stats['scanned'];
            $notScannedInGroup = $totalInGroup - $scannedInGroup;

            // Scan status per requirements:
            if ($totalInGroup === 1) {
                $scanStatus = $scannedInGroup === 1 ? 'Scanned' : 'Not Scanned';
            } else {
                if ($scannedInGroup === $totalInGroup) {
                    $scanStatus = 'Scanned';
                } elseif ($scannedInGroup === 0) {
                    $scanStatus = 'Not Scanned';
                } else {
                    $scanStatus = "{$scannedInGroup} Scanned / {$notScannedInGroup} Not Scanned";
                }
            }

            // Remarks per requirements:
            // Leave blank when all assets are scanned. If some assets are not scanned, indicate the count, such as "5 Not Scanned".
            // Do not use "Not Found" in the cross-check feature.
            $remarks = $notScannedInGroup > 0 ? "{$notScannedInGroup} Not Scanned" : '';

            $classificationId = (int) $row->PropertyClassificationID;
            $rpcType = $classificationId === self::CLASSIFICATION_NON_EXPENDABLE ? 'RPCPPE' : 'RPCSEP';

            if ($rpcType === 'RPCPPE') {
                $rpcppeCount += $bucket['quantity'];
            } else {
                $rpcsepCount += $bucket['quantity'];
            }

            $article = trim((string) ($row->ArticleName ?: ($row->DrItemDescription ?: $row->InventoryDescription)));
            $brand = trim((string) ($row->Brand ?? ''));
            $model = trim((string) ($row->ModelNumber ?? ''));
            $serial = trim((string) ($row->SerialNumber ?? ''));

            $descParts = array_values(array_filter([$article, $brand, $model, $serial], fn ($v) => $v !== ''));
            $combinedDescription = implode(', ', $descParts);
            if ($combinedDescription === '') {
                $combinedDescription = '-';
            }

            $unitCost = $row->UnitCost !== null ? (float) $row->UnitCost : 0.0;
            $quantity = $bucket['quantity'];
            $itemTotalCost = $unitCost * $quantity;
            $totalCost += $itemTotalCost;

            $endUser = trim((string) ($row->AssignedReceiverName ?: ($row->FallbackEndUserName ?: '')));
            if ($endUser === '') {
                $endUser = '-';
            }

            $propNums = array_values(array_filter($bucket['propertyNumbers']));
            if (count($propNums) > 1) {
                $firstPn = $propNums[0];
                $lastPn = end($propNums);
                if (preg_match('/^(.*?)-(\d+)$/', $firstPn, $m1) && preg_match('/^(.*?)-(\d+)$/', $lastPn, $m2) && $m1[1] === $m2[1]) {
                    $propNumDisplay = $firstPn . '/' . $m2[2];
                } else {
                    $propNumDisplay = $firstPn . '/' . $lastPn;
                }
            } else {
                $propNumDisplay = $propNums[0] ?? '-';
            }

            $items[] = [
                'inventory_detail_id' => (int) $row->InventoryDetailID,
                'po_number' => $row->PONumber ? trim((string) $row->PONumber) : '-',
                'supplier' => $row->SupplierName ? trim((string) $row->SupplierName) : '-',
                'par_ics_number' => $row->ParIcsNumber ? trim((string) $row->ParIcsNumber) : '-',
                'quantity' => $quantity,
                'unit' => $row->Unit ? trim((string) $row->Unit) : 'unit',
                'description' => $combinedDescription,
                'property_number' => $propNumDisplay,
                'unit_cost' => $unitCost,
                'total_cost' => $itemTotalCost,
                'date_received' => $row->DateReceived ? (string) $row->DateReceived : '-',
                'remarks' => $remarks,
                'end_user' => $endUser,
                'scan_status' => $scanStatus,
                'rpc_type' => $rpcType,
                'property_type' => $row->PropertyTypeName ?? '-',
            ];
        }

        return response()->json([
            'department' => [
                'DepartmentID' => (int) $department->DepartmentID,
                'DepartmentName' => (string) $department->DepartmentName,
                'DepartmentCode' => (string) ($department->DepartmentCode ?? ''),
                'DepartmentHead' => $departmentHead ? (string) $departmentHead : null,
            ],
            'summary' => [
                'total_items' => array_sum(array_column($items, 'quantity')),
                'total_cost' => round($totalCost, 2),
                'rpcppe_count' => $rpcppeCount,
                'rpcsep_count' => $rpcsepCount,
            ],
            'data' => $items,
        ]);
    }

    public function auditLogs(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin();
        RpcInventoryCycle::findOrFail($id);

        $perPage = max(10, min(200, (int) $request->query('per_page', 100)));

        $query = RpcInventoryAuditLog::query()
            ->where('rpc_inventory_cycle_id', $id)
            ->with([
                'performedBy:UserID,FullName',
                'condition:ConditionID,ConditionName',
                'previousCondition:ConditionID,ConditionName',
                'scan:id,property_number,inventory_detail_id,condition_id,rpc_type,article,description,accountable_officer,remarks',
                'scan.condition:ConditionID,ConditionName',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('AuditID');

        return response()->json($query->paginate($perPage));
    }

    private function authorizeAdmin()
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        // Allow both Admin and GSO Staff to perform administrative actions
        if (!in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true)) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }

        return $user;
    }

    private function resolveAuditSource(): string
    {
        $source = strtolower(trim((string) request()->header('X-Client-Source', 'web')));

        return in_array($source, ['web', 'mobile', 'api'], true) ? $source : 'web';
    }

    /**
     * @param array<string, mixed> $details
     */
    private function createAuditEntry(
        int $cycleId,
        string $action,
        User $user,
        array $details = [],
        ?RpcInventoryScan $scan = null,
        ?int $conditionId = null,
        ?int $previousConditionId = null,
        ?string $previousRemarks = null,
        ?string $newRemarks = null
    ): RpcInventoryAuditLog {
        return RpcInventoryAuditLog::createLog([
            'rpc_inventory_cycle_id' => $cycleId,
            'rpc_inventory_scan_id' => $scan?->id,
            'action' => $action,
            'source' => $this->resolveAuditSource(),
            'performed_by_user_id' => (int) $user->UserID,
            'performed_by_device_id' => trim((string) request()->header('X-Device-ID', '')) ?: null,
            'inventory_detail_id' => $scan?->inventory_detail_id ? (int) $scan->inventory_detail_id : ($details['inventory_detail_id'] ?? null),
            'property_id' => $scan?->property_id ? (int) $scan->property_id : ($details['property_id'] ?? null),
            'property_number' => $scan?->property_number ?? ($details['property_number'] ?? null),
            'condition_id' => $conditionId ?? ($scan?->condition_id ? (int) $scan->condition_id : null),
            'previous_condition_id' => $previousConditionId,
            'previous_remarks' => $previousRemarks,
            'new_remarks' => $newRemarks,
            'details' => $details,
        ]);
    }

    private function authorizeGso()
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true)) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }

        return $user;
    }

    private function cycleLabel(int $startYear, int $endYear): string
    {
        // Use calendar year only for labels.
        return sprintf('Inventory Cycle %d', $startYear);
    }

    private function extractPropertyId(string $raw): ?int
    {
        $value = trim($raw);
        if ($value === '') {
            return null;
        }

        // Only accept direct numeric IDs, not property numbers with dashes or letters
        if (ctype_digit($value)) {
            $id = (int) $value;
            return $id > 0 ? $id : null;
        }

        // Match /qr/scan/{propertyId} URLs
        if (preg_match('/\/qr\/scan\/(\d+)/', $value, $matches) === 1 && isset($matches[1])) {
            $id = (int) $matches[1];
            return $id > 0 ? $id : null;
        }

        // Handle full URLs with query parameters or inventory paths
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $parts = parse_url($value);
            
            // First priority: ?detail={inventoryDetailId} query parameter
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query);
                if (!empty($query['detail']) && ctype_digit((string) $query['detail'])) {
                    $detailId = (int) $query['detail'];
                    return $detailId > 0 ? $detailId : null;
                }
            }

            // Second priority: /inventory/{inventoryDetailId} path pattern
            if (!empty($parts['path'])) {
                if (preg_match('/\/inventory\/(\d+)/', $parts['path'], $m2) === 1 && isset($m2[1])) {
                    $id2 = (int) $m2[1];
                    return $id2 > 0 ? $id2 : null;
                }
            }
        }

        // For non-URLs, do NOT attempt to extract random numbers - return null
        // to allow the backend to treat this as a property_number instead.
        // This prevents "2026-000002" from being incorrectly parsed as "2026".
        return null;
    }

    private function resolveAssignedProperty(int $propertyId): ?AssignedProperty
    {
        return AssignedProperty::query()
            ->with([
                'condition:ConditionID,ConditionName',
                'inventoryDetail.condition:ConditionID,ConditionName',
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->find($propertyId);
    }

    private function resolveAssignedPropertyByPropertyNumber(string $propertyNumber): ?AssignedProperty
    {
        $normalized = trim($propertyNumber);
        if ($normalized === '') {
            return null;
        }

        return AssignedProperty::query()
            ->with([
                'condition:ConditionID,ConditionName',
                'inventoryDetail.condition:ConditionID,ConditionName',
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->whereHas('inventoryDetail', function ($query) use ($normalized) {
                $query->where('PropertyNumber', $normalized);
            })
            ->orderByDesc('PropertyID')
            ->first();
    }

    private function resolveInventoryDetailById(int $inventoryDetailId): ?PropertyInventoryDetail
    {
        return PropertyInventoryDetail::query()
            ->with([
                'condition:ConditionID,ConditionName',
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.condition:ConditionID,ConditionName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->find($inventoryDetailId);
    }

    private function resolveInventoryDetailByPropertyNumber(string $propertyNumber): ?PropertyInventoryDetail
    {
        $normalized = trim($propertyNumber);
        if ($normalized === '') {
            return null;
        }

        return PropertyInventoryDetail::query()
            ->with([
                'condition:ConditionID,ConditionName',
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.condition:ConditionID,ConditionName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->where('PropertyNumber', $normalized)
            ->orderByDesc('InventoryDetailID')
            ->first();
    }

    /**
     * @return array<string, int|string|float|null>
     */
    private function mapAssignedProperty(AssignedProperty $property): array
    {
        $detail = $property->inventoryDetail;
        $propertyType = $detail?->propertyType;
        $classificationId = $propertyType?->PropertyClassificationID
            ? (int) $propertyType->PropertyClassificationID
            : null;

        $rpcType = $this->determineRpcType($classificationId);
        $fallback = $this->resolveCustodianFallbackByInventoryDetailId((int) ($detail?->InventoryDetailID ?? 0));
        $departmentId = $property->DepartmentID ? (int) $property->DepartmentID : ($fallback['department_id'] ?? null);
        $baseName = $property->endUser?->FullName ?: ($fallback['name'] ?? null);
        $conditionId = $property->ConditionID ? (int) $property->ConditionID : ($detail?->ConditionID ? (int) $detail->ConditionID : null);
        $conditionName = $property->condition?->ConditionName ?: ($detail?->condition?->ConditionName ?: null);

        return [
            'property_id' => (int) $property->PropertyID,
            'inventory_detail_id' => (int) ($property->InventoryDetailID ?? 0),
            'source_dr_detail_id' => $detail?->SourceDRDetailID ? (int) $detail->SourceDRDetailID : null,
            'source_iar_detail_id' => $detail?->SourceIARDetailID ? (int) $detail->SourceIARDetailID : null,
            'property_classification_id' => $classificationId,
            'property_type_name' => $propertyType?->PropertyName,
            'condition_id' => $conditionId,
            'condition_name' => $conditionName,
            'rpc_type' => $rpcType,
            'article' => $detail?->drDetail?->article?->ArticleName
                ?? $detail?->drDetail?->ItemDescription
                ?? $detail?->Description
                ?? $property->Description,
            'description' => $this->composeAssetDescription(
                $detail?->Description ?? $property->Description,
                $detail?->Brand,
                $detail?->ModelNumber,
                $detail?->SerialNumber
            ),
            'property_number' => $detail?->PropertyNumber,
            'unit_of_measure' => $detail?->unitOfMeasure?->UnitName,
            'unit_value' => $detail?->UnitCost !== null ? (float) $detail->UnitCost : null,
            'accountable_officer' => $this->composeOfficerDisplayName(
                $this->resolveCurrentDepartmentOfficerName($departmentId, $baseName),
                $property->department?->DepartmentName ?: ($fallback['department_name'] ?? null)
            ),
            'end_user_id' => $property->EndUserID ? (int) $property->EndUserID : null,
            'department_id' => $departmentId,
            'department_name' => $property->department?->DepartmentName ?: ($fallback['department_name'] ?? null),
            'date_purchased' => $detail?->drDetail?->deliveryReceipt?->DateReceived ? (string) $detail->drDetail->deliveryReceipt->DateReceived : null,
        ];
    }

    /**
     * @return array<string, int|string|float|null>
     */
    private function mapInventoryDetail(PropertyInventoryDetail $detail): array
    {
        $propertyType = $detail->propertyType;
        $classificationId = $propertyType?->PropertyClassificationID
            ? (int) $propertyType->PropertyClassificationID
            : null;

        $rpcType = $this->determineRpcType($classificationId);
        $latestAssigned = $detail->assignedProperties
            ->sortByDesc('PropertyID')
            ->first();
        $fallback = $this->resolveCustodianFallbackByInventoryDetailId((int) ($detail->InventoryDetailID ?? 0));
        $departmentId = $latestAssigned?->DepartmentID ? (int) $latestAssigned->DepartmentID : ($fallback['department_id'] ?? null);
        $baseName = $latestAssigned?->endUser?->FullName ?: ($fallback['name'] ?? null);
        $conditionId = $detail->ConditionID ? (int) $detail->ConditionID : ($latestAssigned?->ConditionID ? (int) $latestAssigned->ConditionID : null);
        $conditionName = $detail->condition?->ConditionName ?: ($latestAssigned?->condition?->ConditionName ?: null);

        return [
            'property_id' => $latestAssigned ? (int) $latestAssigned->PropertyID : null,
            'inventory_detail_id' => (int) ($detail->InventoryDetailID ?? 0),
            'source_dr_detail_id' => $detail->SourceDRDetailID ? (int) $detail->SourceDRDetailID : null,
            'source_iar_detail_id' => $detail->SourceIARDetailID ? (int) $detail->SourceIARDetailID : null,
            'property_classification_id' => $classificationId,
            'property_type_name' => $propertyType?->PropertyName,
            'condition_id' => $conditionId,
            'condition_name' => $conditionName,
            'rpc_type' => $rpcType,
            'article' => $detail->drDetail?->article?->ArticleName
                ?? $detail->drDetail?->ItemDescription
                ?? $detail->Description,
            'description' => $this->composeAssetDescription(
                $detail->Description,
                $detail->Brand,
                $detail->ModelNumber,
                $detail->SerialNumber
            ),
            'property_number' => $detail->PropertyNumber,
            'unit_of_measure' => $detail->unitOfMeasure?->UnitName,
            'unit_value' => $detail->UnitCost !== null ? (float) $detail->UnitCost : null,
            'accountable_officer' => $this->composeOfficerDisplayName(
                $this->resolveCurrentDepartmentOfficerName($departmentId, $baseName),
                $latestAssigned?->department?->DepartmentName ?: ($fallback['department_name'] ?? null)
            ),
            'end_user_id' => $latestAssigned?->EndUserID ? (int) $latestAssigned->EndUserID : null,
            'department_id' => $departmentId,
            'department_name' => $latestAssigned?->department?->DepartmentName ?: ($fallback['department_name'] ?? null),
            'date_purchased' => $detail?->drDetail?->deliveryReceipt?->DateReceived ? (string) $detail->drDetail->deliveryReceipt->DateReceived : null,
        ];
    }

    private function composeOfficerDisplayName(?string $officerName, ?string $departmentName): ?string
    {
        $name = trim((string) $officerName);
        $department = trim((string) $departmentName);

        if ($name === '' && $department === '') {
            return null;
        }

        if ($name !== '' && $department !== '') {
            return $name . ' - ' . $department;
        }

        return $name !== '' ? $name : $department;
    }

    /**
     * @param array<string, int|string|float|null> $seedMapped
     * @return array{
     *     group_total_count:int,
     *     group_scanned_count:int,
     *     group_remaining_count:int,
     *     candidates:array<int, array<string, int|string|bool|null>>,
     *     mapped_by_detail:array<int, array<string, int|string|float|null>>,
     *     existing_scans_by_detail:array<int, RpcInventoryScan>
     * }
     */
    private function resolveScanGroupContext(array $seedMapped, int $cycleId): array
    {
        // Build group context: include seed mapped item and any sibling inventory details
        // that originate from the same Source DR/IAR detail. This allows remarks
        // to be propagated across siblings while still enforcing one-asset-per-scan
        // when creating new scans.
        $seedDetailId = (int) ($seedMapped['inventory_detail_id'] ?? 0);
        $mappedByDetail = [];
        if ($seedDetailId > 0) {
            $mappedByDetail[$seedDetailId] = $seedMapped;
        }

        $seedSourceDr = $seedMapped['source_dr_detail_id'] ?? null;
        $seedSourceIar = $seedMapped['source_iar_detail_id'] ?? null;

        // If the seed references a source DR/IAR, include other inventory details
        // that share the same source identifiers as siblings.
        if ($seedDetailId > 0 && ($seedSourceDr || $seedSourceIar)) {
            $query = PropertyInventoryDetail::query()
                ->with([
                    'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                    'drDetail.article:ArticleID,ArticleName',
                    'unitOfMeasure:UnitOfMeasureID,UnitName',
                    'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                    'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
                ]);

            $query->where(function ($q) use ($seedSourceDr, $seedSourceIar) {
                if ($seedSourceDr) {
                    $q->orWhere('SourceDRDetailID', $seedSourceDr);
                }
                if ($seedSourceIar) {
                    $q->orWhere('SourceIARDetailID', $seedSourceIar);
                }
            });

            $siblings = $query->get();
            foreach ($siblings as $detail) {
                $detailId = (int) ($detail->InventoryDetailID ?? 0);
                if ($detailId <= 0) {
                    continue;
                }

                // Map the inventory detail into the same mapped shape as the seed
                $mapped = $this->mapInventoryDetail($detail);
                $mappedByDetail[$detailId] = $mapped;
            }
        }

        // Fallback: ensure at least the seed exists in mapped_by_detail
        if (empty($mappedByDetail) && $seedDetailId > 0) {
            $mappedByDetail[$seedDetailId] = $seedMapped;
        }

        // Load any existing scans for the mapped inventory details in this cycle
        $existingScansByDetail = [];
        if (!empty($mappedByDetail)) {
            $existing = RpcInventoryScan::query()
                ->where('rpc_inventory_cycle_id', $cycleId)
                ->whereIn('inventory_detail_id', array_keys($mappedByDetail))
                ->get()
                ->keyBy('inventory_detail_id')
                ->all();

            $existingScansByDetail = $existing;
        }

        $candidates = [];
        foreach ($mappedByDetail as $did => $m) {
            $existingScan = $existingScansByDetail[$did] ?? null;
            $candidates[] = [
                'inventory_detail_id' => $did,
                'property_id' => isset($m['property_id']) && $m['property_id'] !== null ? (int) $m['property_id'] : null,
                'property_number' => $m['property_number'] ?? null,
                'is_scanned' => (bool) $existingScan,
                'existing_scan_id' => $existingScan ? (int) $existingScan->id : null,
            ];
        }

        $groupTotalCount = count($candidates);
        $groupScannedCount = count($existingScansByDetail);

        return [
            'group_total_count' => $groupTotalCount,
            'group_scanned_count' => $groupScannedCount,
            'group_remaining_count' => max(0, $groupTotalCount - $groupScannedCount),
            'candidates' => $candidates,
            'mapped_by_detail' => $mappedByDetail,
            'existing_scans_by_detail' => $existingScansByDetail,
        ];
    }

    /**
     * @param array<string, int|string|float|null> $mapped
     */
    private function buildScanGroupingKey(array $mapped): string
    {
        return implode('|', [
            strtolower(trim((string) ($mapped['rpc_type'] ?? ''))),
            strtolower(trim((string) ($mapped['property_type_name'] ?? ''))),
            strtolower(trim((string) ($mapped['article'] ?? ''))),
            strtolower(trim((string) ($mapped['description'] ?? ''))),
            strtolower(trim((string) ($mapped['unit_of_measure'] ?? ''))),
            number_format((float) ($mapped['unit_value'] ?? 0), 4, '.', ''),
            strtolower(trim((string) ($mapped['accountable_officer'] ?? ''))),
            strtolower(trim((string) ($mapped['date_purchased'] ?? ''))),
        ]);
    }

    /**
     * @param array<string, int|string|float|null> $mapped
     */
    private function buildScanRelaxedGroupingKey(array $mapped): string
    {
        return implode('|', [
            strtolower(trim((string) ($mapped['rpc_type'] ?? ''))),
            strtolower(trim((string) ($mapped['article'] ?? ''))),
            strtolower(trim((string) ($mapped['description'] ?? ''))),
            strtolower(trim((string) ($mapped['unit_of_measure'] ?? ''))),
            number_format((float) ($mapped['unit_value'] ?? 0), 4, '.', ''),
            strtolower(trim((string) ($mapped['date_purchased'] ?? ''))),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function aggregateRpcRows(array $rows): array
    {
        $groups = [];

        foreach ($rows as $row) {
            $key = implode('|', [
                strtolower(trim((string) ($row['property_type'] ?? ''))),
                strtolower(trim((string) ($row['article'] ?? ''))),
                strtolower(trim((string) ($row['description'] ?? ''))),
                strtolower(trim((string) ($row['unit_of_measure'] ?? ''))),
                number_format((float) ($row['unit_value'] ?? 0), 4, '.', ''),
                strtolower(trim((string) ($row['accountable_officer'] ?? ''))),
                strtolower(trim((string) ($row['date_purchased'] ?? ''))),
            ]);

            if (!isset($groups[$key])) {
                $seedRow = $row;
                $seedRow['quantity_per_card'] = 0;
                $seedRow['quantity_per_count'] = 0;
                $seedRow['shortage_overage_qty'] = 0;
                $seedRow['shortage_overage_value'] = 0;
                $seedRow['is_scanned'] = false;

                $groups[$key] = [
                    'row' => $seedRow,
                    'property_numbers' => [],
                    'remarks' => [],
                    'remarks_by_property' => [],
                    'conditions' => [],
                ];
            }

            $groups[$key]['property_numbers'][] = trim((string) ($row['property_number'] ?? ''));
            $groups[$key]['row']['quantity_per_card'] = (int) ($groups[$key]['row']['quantity_per_card'] ?? 0) + (int) ($row['quantity_per_card'] ?? 0);
            $groups[$key]['row']['quantity_per_count'] = (int) ($groups[$key]['row']['quantity_per_count'] ?? 0) + (int) ($row['quantity_per_count'] ?? 0);
            $groups[$key]['row']['is_scanned'] = (bool) ($groups[$key]['row']['is_scanned'] ?? false) || (bool) ($row['is_scanned'] ?? false);

            $remarks = trim((string) ($row['remarks'] ?? ''));
            if ($remarks !== '') {
                $groups[$key]['remarks'][$remarks] = true;
            }
            $propNum = trim((string) ($row['property_number'] ?? ''));
            if ($propNum !== '') {
                // Store remark, scanned flag and scanned timestamp so aggregation
                // can compute per-property remarks and determine which
                // properties were actually scanned.
                $groups[$key]['remarks_by_property'][$propNum] = [
                    'remark' => $remarks !== '' ? $remarks : null,
                    'scanned_at' => $row['scanned_at'] ?? null,
                    'is_scanned' => !empty($row['is_scanned']),
                ];
            }

            $condition = trim((string) ($row['condition'] ?? ''));
            if ($condition !== '') {
                $groups[$key]['conditions'][$condition] = true;
            }
        }

        $result = [];

        foreach ($groups as $group) {
            $finalRow = $group['row'];
            $finalRow['property_number'] = $this->summarizePropertyNumberRange($group['property_numbers']);

            $qtyCard = (int) ($finalRow['quantity_per_card'] ?? 0);
            $qtyCount = (int) ($finalRow['quantity_per_count'] ?? 0);
            $unitValue = (float) ($finalRow['unit_value'] ?? 0);
            $shortage = $qtyCount - $qtyCard;

            $finalRow['shortage_overage_qty'] = $shortage;
            $finalRow['shortage_overage_value'] = round($shortage * $unitValue, 2);

            // Derive a single remarks value for the grouped row.
            // If any sibling property has a non-empty remark, surface the latest
            // non-empty remark only. Otherwise show a single "Not Found" value.
            $perPropertyRemarks = $group['remarks_by_property'] ?? [];

            // Find the non-empty remark with the latest scanned_at timestamp.
            $latestRemark = null;
            $latestTs = 0;
            foreach ($perPropertyRemarks as $pn => $info) {
                $rm = is_array($info) && array_key_exists('remark', $info) ? $info['remark'] : null;
                // Prefer server-created timestamp when available for authoritative ordering.
                $tsRaw = null;
                if (is_array($info)) {
                    if (array_key_exists('scan_updated_at', $info) && !empty($info['scan_updated_at'])) {
                        $tsRaw = $info['scan_updated_at'];
                    } elseif (array_key_exists('scan_created_at', $info) && !empty($info['scan_created_at'])) {
                        $tsRaw = $info['scan_created_at'];
                    } elseif (array_key_exists('scanned_at', $info) && !empty($info['scanned_at'])) {
                        $tsRaw = $info['scanned_at'];
                    }
                }
                $clean = is_string($rm) ? trim($rm) : null;
                if ($clean === null || $clean === '') {
                    continue;
                }

                $ts = 0;
                if (is_string($tsRaw) && $tsRaw !== '') {
                    try {
                        $ts = \Carbon\Carbon::parse($tsRaw)->getTimestamp();
                    } catch (\Exception $e) {
                        $ts = 0;
                    }
                }

                // Prefer greater or equal so later iterations with equal timestamps
                // will override (last-wins) which aligns with user expectation.
                if ($ts >= $latestTs) {
                    $latestTs = $ts;
                    $latestRemark = $clean;
                }
            }

            // Build a simple map of per-property remarks for the frontend to
            // render per-number notes when needed.
            $perPropertyRemarksMap = [];
            foreach ($group['remarks_by_property'] as $pn => $info) {
                $perPropertyRemarksMap[$pn] = is_array($info) && array_key_exists('remark', $info) ? $info['remark'] : null;
            }

            // Determine Not Found counts: only consider valid property numbers.
            $uniqueNums = array_values(array_filter(array_unique(array_map(static function (string $v) {
                return trim($v);
            }, $group['property_numbers'])), static function (string $value) {
                return $value !== '' && $value !== '-';
            }));

            $totalProps = count($uniqueNums);
            $scannedCount = 0;
            if ($totalProps > 0) {
                foreach ($uniqueNums as $pn) {
                    if (isset($group['remarks_by_property'][$pn]) && is_array($group['remarks_by_property'][$pn]) && !empty($group['remarks_by_property'][$pn]['is_scanned'])) {
                        $scannedCount++;
                    }
                }
            }

            if ($latestRemark !== null) {
                $finalRow['remarks'] = $latestRemark;
            } elseif ($totalProps === 0) {
                // No property numbers to base counts on — fallback to previous behavior.
                $finalRow['remarks'] = 'Not Found';
            } elseif ($scannedCount === 0) {
                // All properties unscanned
                $finalRow['remarks'] = 'Not Found';
            } elseif ($scannedCount < $totalProps) {
                // Some scanned — show remaining not found count
                $notFound = $totalProps - $scannedCount;
                $finalRow['remarks'] = sprintf('%d Not Found', $notFound);
            } else {
                // All scanned and no custom remark
                $finalRow['remarks'] = null;
            }

            $finalRow['property_remarks'] = $perPropertyRemarksMap;

            $conditionList = array_keys($group['conditions']);
            $finalRow['condition'] = count($conditionList) > 0
                ? $conditionList[0]
                : null;

            $result[] = $finalRow;
        }

        return $result;
    }

    /**
     * @param array<int, string> $propertyNumbers
     */
    private function summarizePropertyNumberRange(array $propertyNumbers): string
    {
        $normalized = array_values(array_filter(array_unique(array_map(static function (string $value) {
            return trim($value);
        }, $propertyNumbers)), static function (string $value) {
            return $value !== '' && $value !== '-';
        }));

        if (count($normalized) === 0) {
            return '-';
        }

        sort($normalized, SORT_NATURAL | SORT_FLAG_CASE);
        if (count($normalized) === 1) {
            return $normalized[0];
        }

        $parsed = [];
        foreach ($normalized as $value) {
            if (preg_match('/^(.*?)(\d+)$/', $value, $match) !== 1) {
                return implode(', ', $this->compactPropertyNumberList($normalized));
            }

            $parsed[] = [
                'prefix' => (string) ($match[1] ?? ''),
                'number' => (int) ($match[2] ?? 0),
                'width' => strlen((string) ($match[2] ?? '')),
                'raw' => $value,
            ];
        }

        $prefixes = array_values(array_unique(array_map(static function (array $item): string {
            return $item['prefix'];
        }, $parsed)));

        if (count($prefixes) !== 1) {
            return implode(', ', $this->compactPropertyNumberList($normalized));
        }

        usort($parsed, static function (array $a, array $b): int {
            return $a['number'] <=> $b['number'];
        });

        $isContiguous = true;
        for ($i = 1; $i < count($parsed); $i++) {
            if (($parsed[$i]['number'] - $parsed[$i - 1]['number']) !== 1) {
                $isContiguous = false;
                break;
            }
        }

        if (! $isContiguous) {
            return implode(', ', $this->compactPropertyNumberList($normalized));
        }

        $prefix = (string) $parsed[0]['prefix'];
        $width = (int) $parsed[0]['width'];
        $firstNum = str_pad((string) $parsed[0]['number'], $width, '0', STR_PAD_LEFT);
        $lastNum = str_pad((string) $parsed[count($parsed) - 1]['number'], $width, '0', STR_PAD_LEFT);

        return $prefix . $firstNum . '/' . $lastNum;
    }

    /**
     * @param array<int, string> $numbers
     * @return array<int, string>
     */
    private function compactPropertyNumberList(array $numbers): array
    {
        $total = count($numbers);
        if ($total <= 5) {
            return $numbers;
        }

        $visible = array_slice($numbers, 0, 5);
        $visible[] = sprintf('... (%d items)', $total);

        return $visible;
    }

    private function composeAssetDescription(?string $baseDescription, ?string $brand, ?string $model, ?string $serial): string
    {
        $base = trim((string) ($baseDescription ?? ''));

        if ($base !== '') {
            return $base;
        }

        $parts = array_values(array_filter([
            trim((string) ($brand ?? '')),
            trim((string) ($model ?? '')),
            trim((string) ($serial ?? '')),
        ], static function (string $value) {
            return $value !== '';
        }));

        if (count($parts) === 0) {
            return '-';
        }

        return implode(' / ', $parts);
    }

    private function resolveCurrentDepartmentOfficerName(?int $departmentId, ?string $fallbackName): ?string
    {
        $normalizedFallback = is_string($fallbackName) ? trim($fallbackName) : null;

        if (!$departmentId || $departmentId <= 0) {
            return $normalizedFallback !== '' ? $normalizedFallback : null;
        }

        if (array_key_exists($departmentId, $this->departmentOfficerNameCache)) {
            return $this->departmentOfficerNameCache[$departmentId] ?: ($normalizedFallback !== '' ? $normalizedFallback : null);
        }

        $currentName = DB::table('user')
            ->where('DepartmentID', $departmentId)
            ->where('IsActive', 1)
            ->whereNotNull('FullName')
            ->whereRaw("TRIM(FullName) <> ''")
            ->orderByRaw('CASE WHEN UserRoleID = 4 THEN 0 WHEN UserRoleID = 5 THEN 1 ELSE 2 END')
            ->orderByDesc('UpdatedDate')
            ->orderByDesc('UserID')
            ->value('FullName');

        $resolved = is_string($currentName) ? trim($currentName) : null;
        $this->departmentOfficerNameCache[$departmentId] = $resolved !== '' ? $resolved : null;

        return $this->departmentOfficerNameCache[$departmentId] ?: ($normalizedFallback !== '' ? $normalizedFallback : null);
    }

    /**
     * @return array{name: string|null, department_id: int|null, department_name: string|null}
     */
    private function resolveCustodianFallbackByInventoryDetailId(int $inventoryDetailId): array
    {
        if ($inventoryDetailId <= 0) {
            return [
                'name' => null,
                'department_id' => null,
                'department_name' => null,
            ];
        }

        $issuance = DB::table('propertyissuancedetail as pidet')
            ->join('propertyissuance as pi', 'pi.IssuanceID', '=', 'pidet.IssuanceID')
            ->leftJoin('assetrequest as ar', 'ar.RequestID', '=', 'pi.RequestID')
            ->leftJoin('user as reqU', 'reqU.UserID', '=', 'ar.RequestedBy')
            ->leftJoin('enduser as eu', 'eu.EndUserID', '=', 'pi.IssuedTo')
            ->leftJoin('department as dep', 'dep.DepartmentID', '=', 'eu.DepartmentID')
            ->leftJoin('user as u', 'u.UserID', '=', 'pi.IssuedTo')
            ->leftJoin('department as depU', 'depU.DepartmentID', '=', 'u.DepartmentID')
            ->leftJoin('department as depReq', 'depReq.DepartmentID', '=', 'ar.DepartmentID')
            ->where('pidet.InventoryDetailID', $inventoryDetailId)
            ->whereNull('pi.deleted_at')
            ->whereNull('pidet.deleted_at')
            ->whereIn('pi.Status', ['pending_acknowledgment', 'acknowledged'])
            ->orderByDesc('pi.DateIssued')
            ->orderByDesc('pi.IssuanceID')
            ->select([
                'pi.DocumentType as document_type',
                'reqU.FullName as request_user_name',
                'eu.FullName as end_user_name',
                'u.FullName as issued_to_user_name',
                'pi.IssuedToName as issued_to_name',
                'dep.DepartmentID as dep_end_user_id',
                'dep.DepartmentName as dep_end_user_name',
                'depU.DepartmentID as dep_user_id',
                'depU.DepartmentName as dep_user_name',
                'depReq.DepartmentID as dep_request_id',
                'depReq.DepartmentName as dep_request_name',
            ])
            ->first();

        if ($issuance) {
            $name = null;
            if ((string) $issuance->document_type === 'ICS') {
                $name = $issuance->request_user_name ?: null;
            }

            if (!$name) {
                $name = $issuance->end_user_name
                    ?: $issuance->issued_to_user_name
                    ?: $issuance->issued_to_name
                    ?: $issuance->request_user_name
                    ?: null;
            }

            $departmentId = $issuance->dep_end_user_id
                ?: $issuance->dep_user_id
                ?: $issuance->dep_request_id
                ?: null;

            $departmentName = $issuance->dep_end_user_name
                ?: $issuance->dep_user_name
                ?: $issuance->dep_request_name
                ?: null;

            return [
                'name' => $name ? (string) $name : null,
                'department_id' => $departmentId ? (int) $departmentId : null,
                'department_name' => $departmentName ? (string) $departmentName : null,
            ];
        }

        $fromDelivery = DB::table('propertyinventorydetail as invd')
            ->join('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'invd.SourceDRDetailID')
            ->join('deliveryreceipt as dr', 'dr.DRID', '=', 'drd.DRID')
            ->leftJoin('user as reqU', 'reqU.UserID', '=', 'dr.RequestedBy')
            ->leftJoin('department as depReq', 'depReq.DepartmentID', '=', 'reqU.DepartmentID')
            ->leftJoin('user as recU', 'recU.UserID', '=', 'dr.ReceivedBy')
            ->leftJoin('department as depRec', 'depRec.DepartmentID', '=', 'recU.DepartmentID')
            ->where('invd.InventoryDetailID', $inventoryDetailId)
            ->select([
                'reqU.FullName as requested_by_name',
                'recU.FullName as received_by_name',
                'depReq.DepartmentID as dep_requested_id',
                'depReq.DepartmentName as dep_requested_name',
                'depRec.DepartmentID as dep_received_id',
                'depRec.DepartmentName as dep_received_name',
            ])
            ->first();

        if ($fromDelivery) {
            $name = $fromDelivery->requested_by_name ?: ($fromDelivery->received_by_name ?: null);
            $departmentId = $fromDelivery->dep_requested_id ?: ($fromDelivery->dep_received_id ?: null);
            $departmentName = $fromDelivery->dep_requested_name ?: ($fromDelivery->dep_received_name ?: null);

            return [
                'name' => $name ? (string) $name : null,
                'department_id' => $departmentId ? (int) $departmentId : null,
                'department_name' => $departmentName ? (string) $departmentName : null,
            ];
        }

        return [
            'name' => null,
            'department_id' => null,
            'department_name' => null,
        ];
    }

    private function determineRpcType(?int $classificationId): string
    {
        if ($classificationId === self::CLASSIFICATION_NON_EXPENDABLE) {
            return 'RPCPPE';
        }

        if ($classificationId === self::CLASSIFICATION_SEMI_EXPENDABLE) {
            return 'RPCSEP';
        }

        return 'RPCSEP';
    }

    /**
     * Bulk sync endpoint for mobile offline-first scanning.
     * Accepts array of scans with client_uuid for idempotent deduplication.
     */
    public function bulkStoreScan(Request $request, int $id): JsonResponse
{
    $user = $this->authorizeGso();
    $cycle = RpcInventoryCycle::findOrFail($id);
 
    if ($cycle->isClosed()) {
        return response()->json([
            'message' => 'Cannot add scans to a closed inventory cycle. The cycle is locked and read-only.',
            'cycle_status' => $cycle->status,
            'closed_at' => $cycle->closed_at,
        ], 409);
    }
 
    $source = $this->resolveAuditSource();
 
    $validated = $request->validate([
        'scans' => 'required|array|min:1|max:100',
        'scans.*.client_uuid' => 'nullable|string|max:36',
        'scans.*.inventory_detail_id' => 'nullable|integer|min:1',
        'scans.*.property_id' => 'nullable|integer|min:1',
        'scans.*.condition_id' => 'required|integer|exists:condition,ConditionID',
        'scans.*.scanned_at' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
        'scans.*.remarks' => 'nullable|string|max:255',
        'scans.*.device_id' => 'nullable|string|max:100',
    ]);
 
    // -------------------------------------------------------
    // FIX #4: Batch-load all needed inventory details upfront.
    // Collect every inventory_detail_id and property_id first,
    // then load them in two queries instead of N queries.
    // -------------------------------------------------------
    $allInventoryDetailIds = [];
    $allPropertyIds = [];
 
    foreach ($validated['scans'] as $scanData) {
        if (!empty($scanData['inventory_detail_id'])) {
            $allInventoryDetailIds[] = (int) $scanData['inventory_detail_id'];
        }
        if (!empty($scanData['property_id'])) {
            $allPropertyIds[] = (int) $scanData['property_id'];
        }
    }
 
    // One query for all inventory details needed
    $inventoryDetailMap = PropertyInventoryDetail::query()
        ->with([
            'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
            'drDetail.article:ArticleID,ArticleName',
            'unitOfMeasure:UnitOfMeasureID,UnitName',
            'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
            'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
        ])
        ->whereIn('InventoryDetailID', array_unique($allInventoryDetailIds))
        ->get()
        ->keyBy('InventoryDetailID');
 
    // One query for all assigned properties needed (for property_id lookups)
    $assignedPropertyMap = collect([]);
    if (!empty($allPropertyIds)) {
        $assignedPropertyMap = AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->whereIn('PropertyID', array_unique($allPropertyIds))
            ->get()
            ->keyBy('PropertyID');
    }
 
    // Also batch-load existing scans for duplicate detection (one query)
    $existingClientUuids = collect($validated['scans'])
        ->pluck('client_uuid')
        ->filter()
        ->map(fn($u) => trim((string) $u))
        ->filter()
        ->values()
        ->all();
 
    $existingByClientUuid = collect([]);
    if (!empty($existingClientUuids)) {
        $existingByClientUuid = RpcInventoryScan::where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->whereIn('client_uuid', $existingClientUuids)
            ->get()
            ->keyBy('client_uuid');
    }
 
    // Batch-load already-scanned inventory detail IDs for this cycle (one query)
    $alreadyScannedDetailIds = RpcInventoryScan::where('rpc_inventory_cycle_id', (int) $cycle->id)
        ->whereIn('inventory_detail_id', array_unique($allInventoryDetailIds))
        ->pluck('id', 'inventory_detail_id'); // detail_id => scan_id
 
    // -------------------------------------------------------
 
    $results = [];
    $createdCount = 0;
    $duplicateCount = 0;
    $errorCount = 0;
    $syncedPropertyNumbers = [];
    $createdScanIds = [];
 
    DB::transaction(function () use (
        &$results,
        &$createdCount,
        &$duplicateCount,
        &$errorCount,
        &$syncedPropertyNumbers,
        &$createdScanIds,
        $cycle,
        $validated,
        $user,
        $source,
        $inventoryDetailMap,
        $assignedPropertyMap,
        $existingByClientUuid,
        $alreadyScannedDetailIds
    ): void {
        foreach ($validated['scans'] as $scanData) {
            $clientUuid = isset($scanData['client_uuid']) ? trim((string) $scanData['client_uuid']) : null;
            $inventoryDetailId = isset($scanData['inventory_detail_id']) ? (int) $scanData['inventory_detail_id'] : null;
            $propertyId = isset($scanData['property_id']) ? (int) $scanData['property_id'] : null;
            $conditionId = (int) $scanData['condition_id'];
            $deviceId = isset($scanData['device_id']) ? trim((string) $scanData['device_id']) : null;
            $scannedAt = isset($scanData['scanned_at']) ? Carbon::parse($scanData['scanned_at']) : now();
            $remarks = isset($scanData['remarks']) ? trim((string) $scanData['remarks']) : null;
 
            try {
                // FIX #4: Use pre-loaded map instead of individual DB query
                if ($clientUuid && $existingByClientUuid->has($clientUuid)) {
                    $existing = $existingByClientUuid->get($clientUuid);
                    $results[] = [
                        'client_uuid' => $clientUuid,
                        'status' => 'duplicate',
                        'message' => 'Scan already recorded with this client UUID in this cycle',
                        'scan_id' => (int) $existing->id,
                    ];
                    $duplicateCount++;
                    continue;
                }
 
                if (!$inventoryDetailId && !$propertyId) {
                    $results[] = [
                        'client_uuid' => $clientUuid,
                        'status' => 'error',
                        'message' => 'Inventory Detail ID or Property ID is required',
                    ];
                    $errorCount++;
                    continue;
                }
 
                // FIX #4: Use pre-loaded map instead of individual DB query
                $inventoryDetail = null;
                if ($inventoryDetailId && $inventoryDetailMap->has($inventoryDetailId)) {
                    $inventoryDetail = $inventoryDetailMap->get($inventoryDetailId);
                } elseif ($propertyId && $assignedPropertyMap->has($propertyId)) {
                    $assignedProperty = $assignedPropertyMap->get($propertyId);
                    $inventoryDetail = $assignedProperty?->inventoryDetail;
                }
 
                if (!$inventoryDetail) {
                    $results[] = [
                        'client_uuid' => $clientUuid,
                        'status' => 'error',
                        'message' => 'Inventory item not found',
                    ];
                    $errorCount++;
                    continue;
                }
 
                $mapped = $this->mapInventoryDetail($inventoryDetail);
                $resolvedDetailId = (int) $inventoryDetail->InventoryDetailID;
 
                // FIX #4: Use pre-loaded duplicate check instead of individual DB query
                if ($alreadyScannedDetailIds->has($resolvedDetailId)) {
                    $results[] = [
                        'client_uuid' => $clientUuid,
                        'status' => 'duplicate',
                        'message' => 'Inventory detail already scanned in this cycle',
                        'scan_id' => (int) $alreadyScannedDetailIds->get($resolvedDetailId),
                    ];
                    $duplicateCount++;
                    continue;
                }
 
                $scan = RpcInventoryScan::create([
                    'rpc_inventory_cycle_id' => (int) $cycle->id,
                    'inventory_detail_id' => $resolvedDetailId,
                    'property_id' => isset($mapped['property_id']) && $mapped['property_id'] !== null ? (int) $mapped['property_id'] : null,
                    'rpc_type' => (string) $mapped['rpc_type'],
                    'property_classification_id' => isset($mapped['property_classification_id']) && $mapped['property_classification_id'] !== null
                        ? (int) $mapped['property_classification_id']
                        : null,
                    'condition_id' => $conditionId,
                    'scanned_at' => $scannedAt,
                    'scanned_by_user_id' => (int) $user->UserID,
                    'end_user_id' => isset($mapped['end_user_id']) && $mapped['end_user_id'] !== null ? (int) $mapped['end_user_id'] : null,
                    'department_id' => isset($mapped['department_id']) && $mapped['department_id'] !== null ? (int) $mapped['department_id'] : null,
                    'article' => $mapped['article'] ?? null,
                    'description' => $mapped['description'] ?? null,
                    'property_number' => $mapped['property_number'] ?? null,
                    'unit_of_measure' => $mapped['unit_of_measure'] ?? null,
                    'unit_value' => $mapped['unit_value'] ?? null,
                    'quantity_per_card' => 1,
                    'quantity_per_count' => 1,
                    'remarks' => $remarks,
                    'accountable_officer' => $mapped['accountable_officer'] ?? null,
                    'client_uuid' => $clientUuid,
                    'device_id' => $deviceId,
                ]);
 
                // Sync condition to PropertyInventoryDetail and AssignedProperty (Inventory / All Items)
                $this->syncInventoryCondition(
                    (int) $resolvedDetailId,
                    isset($mapped['property_id']) && $mapped['property_id'] !== null ? (int) $mapped['property_id'] : null,
                    (int) $conditionId
                );
 
                // Mark as scanned in our in-memory map so subsequent scans in the
                // same batch don't try to insert the same detail again.
                $alreadyScannedDetailIds->put($resolvedDetailId, (int) $scan->id);
 
                $this->createAuditEntry(
                    (int) $cycle->id,
                    'scan_synced',
                    $user,
                    [
                        'sync_source' => $source,
                        'rpc_type' => (string) ($mapped['rpc_type'] ?? ''),
                        'article' => $mapped['article'] ?? null,
                        'description' => $mapped['description'] ?? null,
                        'accountable_officer' => $mapped['accountable_officer'] ?? null,
                        'client_uuid' => $clientUuid,
                        'device_id' => $deviceId,
                        'status' => 'created',
                        'inventory_detail_id' => $resolvedDetailId,
                        'property_number' => $scan->property_number,
                        'property_id' => $scan->property_id,
                        'condition_id' => $conditionId,
                        'remarks' => $remarks,
                    ],
                    $scan,
                    $conditionId
                );
 
                if ($remarks !== null) {
                    $groupContext = $this->resolveScanGroupContext($mapped, (int) $cycle->id);
                    $existingScansByDetail = $groupContext['existing_scans_by_detail'] ?? [];
                    if (!empty($existingScansByDetail)) {
                        foreach ($existingScansByDetail as $existingDetailId => $existingScan) {
                            if ($existingScan->id === $scan->id) {
                                continue;
                            }
 
                            $previousRemarks = $existingScan->remarks ?? null;
                            if ($previousRemarks === $remarks) {
                                continue;
                            }
 
                            $existingScan->remarks = $remarks;
                            $existingScan->save();
 
                            $this->createAuditEntry(
                                (int) $cycle->id,
                                'scan_updated',
                                $user,
                                [
                                    'inventory_detail_id' => $existingScan->inventory_detail_id,
                                    'property_number' => $existingScan->property_number,
                                ],
                                $existingScan,
                                (int) $existingScan->condition_id,
                                null,
                                $previousRemarks,
                                $remarks
                            );
                        }
                    }
                }
 
                if ($scan->property_number) {
                    $syncedPropertyNumbers[] = (string) $scan->property_number;
                }
 
                $results[] = [
                    'client_uuid' => $clientUuid,
                    'status' => 'created',
                    'message' => 'Scan recorded successfully',
                    'scan_id' => (int) $scan->id,
                ];
                $createdScanIds[] = (int) $scan->id;
                $createdCount++;
            } catch (\Exception $e) {
                $results[] = [
                    'client_uuid' => $clientUuid,
                    'status' => 'error',
                    'message' => 'Error creating scan: ' . $e->getMessage(),
                ];
                $errorCount++;
            }
        }
    });
 
    $batchStatus = 'completed';
    if ($errorCount > 0) {
        $batchStatus = ($createdCount > 0 || $duplicateCount > 0) ? 'partially_completed' : 'failed';
    }
 
    // Only log a batch summary entry if this was a true multi-item batch (>1) or if there were errors/duplicates.
    // For single successful scans, the individual 'scan_synced' audit log is already recorded, avoiding redundant duplicate entries.
    $isMultiScanOrIssue = count($validated['scans']) > 1 || $errorCount > 0 || $duplicateCount > 0;
    if ($isMultiScanOrIssue) {
        $this->createAuditEntry(
            (int) $cycle->id,
            'sync_batch_' . $batchStatus,
            $user,
            [
                'source' => $source,
                'property_number' => count($syncedPropertyNumbers) === 1 ? ($syncedPropertyNumbers[0] ?? 'Batched') : 'Batched',
                'total' => count($validated['scans']),
                'created' => $createdCount,
                'duplicates' => $duplicateCount,
                'errors' => $errorCount,
                'property_numbers' => array_values(array_unique(array_filter($syncedPropertyNumbers))),
                'status' => $batchStatus,
            ]
        );
    }
 
    try {
        \Log::debug('Bulk sync result', [
            'cycle_id' => (int) $cycle->id,
            'created_count' => $createdCount,
            'duplicate_count' => $duplicateCount,
            'error_count' => $errorCount,
            'created_scan_ids' => $createdScanIds,
        ]);
    } catch (\Exception $_e) {
        // swallow logging errors
    }
 
    return response()->json([
        'message' => "Bulk sync processed: {$createdCount} created, {$duplicateCount} skipped, {$errorCount} errors",
        'summary' => [
            'total' => count($validated['scans']),
            'created' => $createdCount,
            'duplicates' => $duplicateCount,
            'errors' => $errorCount,
            'created_scan_ids' => $createdScanIds,
        ],
        'results' => $results,
    ], 201);
}

    /**
     * Close an RPC inventory cycle.
     * Only Administrators can close cycles.
     */
    public function closeCycle(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if ($roleName !== self::ROLE_ADMIN) {
            return response()->json([
                'message' => 'Only Administrators can close inventory cycles.',
            ], 403);
        }

        $validated = $request->validate([
            'password' => 'required|string',
            'close_reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            $cycle = RpcInventoryCycle::findOrFail($id);

            if ($cycle->isClosed()) {
                return response()->json([
                    'message' => 'This inventory cycle is already closed.',
                ], 409);
            }

            // Verify administrator password
            if (!password_verify((string) $validated['password'], (string) $user->Password)) {
                RpcInventoryAuditLog::createLog([
                    'rpc_inventory_cycle_id' => (int) $cycle->id,
                    'action' => 'close_password_failed',
                    'source' => 'web',
                    'performed_by_user_id' => (int) $user->UserID,
                    'details' => [
                        'cycle_label' => $cycle->cycle_label,
                        'reason' => 'Failed administrator password verification during cycle closure',
                    ],
                ]);

                return response()->json([
                    'message' => 'Administrator password verification failed.',
                ], 401);
            }

            DB::beginTransaction();

            // Update cycle to closed
            $cycle->update([
                'status' => 'closed',
                'closed_by_user_id' => (int) $user->UserID,
                'closed_at' => now(),
                'close_reason' => trim((string) $validated['close_reason']),
            ]);

            // Create audit log
            RpcInventoryAuditLog::createLog([
                'rpc_inventory_cycle_id' => (int) $cycle->id,
                'action' => 'cycle_closed',
                'source' => 'web',
                'performed_by_user_id' => (int) $user->UserID,
                'details' => [
                    'cycle_label' => $cycle->cycle_label,
                    'closed_by' => $user->FullName,
                    'close_reason' => $validated['close_reason'],
                    'closed_at' => $cycle->closed_at->toDateTimeString(),
                ],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Inventory cycle closed successfully.',
                'cycle' => $cycle,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error closing RPC cycle', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Error closing inventory cycle: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request to reopen a closed RPC inventory cycle.
     * Returns a summary for Administrator confirmation.
     */
    public function reopenRequest(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if ($roleName !== self::ROLE_ADMIN) {
            return response()->json([
                'message' => 'Only Administrators can reopen inventory cycles.',
            ], 403);
        }

        try {
            $cycle = RpcInventoryCycle::findOrFail($id);

            if (!$cycle->isClosed()) {
                return response()->json([
                    'message' => 'Only closed inventory cycles can be reopened.',
                ], 409);
            }

            if (!$cycle->canReopen()) {
                return response()->json([
                    'message' => 'This inventory cycle has reached the maximum number of reopen attempts and is now permanently closed.',
                    'reopen_count' => (int) $cycle->reopen_count,
                    'max_reopens' => 2,
                    'can_reopen' => false,
                    'is_permanently_closed' => true,
                ], 409);
            }

            // Build a compatible response shape for frontend consumers.
            // Provide both a flattened top-level shape and `closure_info` for older clients.
            $closureInfo = [
                'status' => $cycle->status,
                'closed_by' => $cycle->closedBy?->FullName,
                'closed_at' => $cycle->closed_at ? $cycle->closed_at->toIso8601String() : null,
                'close_reason' => $cycle->close_reason,
                'reopened_by' => $cycle->reopenedBy?->FullName,
                'reopened_at' => $cycle->reopened_at ? $cycle->reopened_at->toIso8601String() : null,
                'reopen_reason' => $cycle->reopen_reason,
                'reopen_count' => (int) $cycle->reopen_count,
                'can_reopen' => $cycle->canReopen(),
                'is_permanently_closed' => $cycle->isPermanentlyClosed(),
            ];

            $topLevel = [
                'status' => $closureInfo['status'],
                'closed_at' => $closureInfo['closed_at'],
                'close_reason' => $closureInfo['close_reason'],
                'close_by_user' => $cycle->closedBy ? [
                    'UserID' => (int) $cycle->closedBy->UserID,
                    'UserName' => (string) $cycle->closedBy->FullName,
                ] : null,
                'reopen_count' => (int) $cycle->reopen_count,
                'can_reopen' => $closureInfo['can_reopen'],
            ];

            return response()->json(array_merge([
                'message' => 'Reopen request info',
                'closure_info' => $closureInfo,
            ], $topLevel), 200);
        } catch (\Exception $e) {
            \Log::error('Error in reopen request', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Error processing reopen request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reopen a closed RPC inventory cycle.
     * Requires password confirmation and optional OTP verification.
     */
    public function reopenCycle(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if ($roleName !== self::ROLE_ADMIN) {
            return response()->json([
                'message' => 'Only Administrators can reopen inventory cycles.',
            ], 403);
        }

        // Allow clients to send either `reopen_reason` or the legacy `reason` field.
        if ($request->has('reason') && !$request->has('reopen_reason')) {
            $request->merge(['reopen_reason' => $request->input('reason')]);
        }

        // Password length validation removed to accept short legacy passwords.
        $validated = $request->validate([
            'password' => 'required|string',
            'reopen_reason' => 'required|string|min:10|max:1000',
            'otp_code' => 'nullable|string|digits:6',
        ]);

        try {
            $cycle = RpcInventoryCycle::findOrFail($id);

            if (!$cycle->isClosed()) {
                return response()->json([
                    'message' => 'Only closed inventory cycles can be reopened.',
                ], 409);
            }

            if (!$cycle->canReopen()) {
                return response()->json([
                    'message' => 'This inventory cycle has reached the maximum number of reopen attempts and is now permanently closed.',
                    'reopen_count' => (int) $cycle->reopen_count,
                    'max_reopens' => 2,
                    'can_reopen' => false,
                    'is_permanently_closed' => true,
                ], 409);
            }

            // Verify password
            if (!password_verify((string) $validated['password'], (string) $user->Password)) {
                RpcInventoryAuditLog::createLog([
                    'rpc_inventory_cycle_id' => (int) $cycle->id,
                    'action' => 'reopen_password_failed',
                    'source' => 'web',
                    'performed_by_user_id' => (int) $user->UserID,
                    'details' => [
                        'cycle_label' => $cycle->cycle_label,
                        'reason' => 'Failed password verification',
                    ],
                ]);

                return response()->json([
                    'message' => 'Password verification failed.',
                ], 401);
            }

            // OTP verification (if configured and provided)
            if (!empty($validated['otp_code'])) {
                // TODO: Implement TOTP verification if OTP is enabled in config
                // For now, OTP is optional, so we just log the attempt
            }

            DB::beginTransaction();

            // Update cycle to active (reopened)
            $cycle->update([
                'status' => 'active',
                'reopened_by_user_id' => (int) $user->UserID,
                'reopened_at' => now(),
                'reopen_reason' => trim((string) $validated['reopen_reason']),
                'reopen_count' => (int) $cycle->reopen_count + 1,
            ]);

            // Create audit log
            RpcInventoryAuditLog::createLog([
                'rpc_inventory_cycle_id' => (int) $cycle->id,
                'action' => 'cycle_reopened',
                'source' => 'web',
                'performed_by_user_id' => (int) $user->UserID,
                'details' => [
                    'cycle_label' => $cycle->cycle_label,
                    'reopened_by' => $user->FullName,
                    'reopen_reason' => $validated['reopen_reason'],
                    'reopened_at' => $cycle->reopened_at->toDateTimeString(),
                    'reopen_count' => $cycle->reopen_count,
                    'otp_verified' => !empty($validated['otp_code']),
                ],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Inventory cycle reopened successfully.',
                'cycle' => $cycle,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors with 422 for client visibility
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reopening RPC cycle', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Error reopening inventory cycle: ' . $e->getMessage(),
            ], 500);
        }
    }
}
