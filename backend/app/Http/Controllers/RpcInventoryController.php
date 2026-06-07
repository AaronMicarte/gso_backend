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
            ->get();

        $scannedByDetailId = $scans->keyBy('inventory_detail_id');
        $lastScanTime = $scans->pluck('scanned_at')->filter()->max();

        $assigned = AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.drDetail:ArticleID,ItemDescription',
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

        return response()->json([
            'total_expected' => $totalExpected,
            'total_scanned' => $scannedTotal,
            'missing_count' => max(0, $totalExpected - $scannedTotal),
            'excess_count' => max(0, $scannedTotal - $totalExpected),
            'completed_locations' => $completedLocations,
            'pending_locations' => $pendingLocations,
            'last_scan_time' => $lastScanTime ? $lastScanTime->toIso8601String() : now()->toIso8601String(),
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

        $groupContext = $this->resolveScanGroupContext($mapped, (int) $cycle->id);

        $duplicate = $groupContext['existing_scans_by_detail'][(int) $mapped['inventory_detail_id']] ?? null;

        return response()->json([
            'asset' => $mapped,
            'already_scanned' => (bool) $duplicate,
            'existing_scan_id' => $duplicate ? (int) $duplicate->id : null,
            'group_total_count' => (int) $groupContext['group_total_count'],
            'group_scanned_count' => (int) $groupContext['group_scanned_count'],
            'group_remaining_count' => (int) $groupContext['group_remaining_count'],
            'candidates' => array_values($groupContext['candidates']),
        ]);
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
            'selected_inventory_detail_ids' => 'nullable|array|min:1',
            'selected_inventory_detail_ids.*' => 'integer|min:1',
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

        $requestedDetailIds = array_values(array_unique(array_map(
            static fn ($value) => (int) $value,
            (array) ($validated['selected_inventory_detail_ids'] ?? [])
        )));

        if (count($requestedDetailIds) === 0) {
            $requestedDetailIds = [(int) $mapped['inventory_detail_id']];
        }

        $mappedByDetail = $groupContext['mapped_by_detail'];
        $invalidDetailIds = array_values(array_filter($requestedDetailIds, static function (int $detailId) use ($mappedByDetail): bool {
            return $detailId <= 0 || !array_key_exists($detailId, $mappedByDetail);
        }));

        if (count($invalidDetailIds) > 0) {
            return response()->json([
                'message' => 'One or more selected property numbers do not belong to this asset group.',
                'invalid_inventory_detail_ids' => $invalidDetailIds,
            ], 422);
        }

        $existingScansByDetail = $groupContext['existing_scans_by_detail'];
        $alreadyScannedDetailIds = array_values(array_filter($requestedDetailIds, static function (int $detailId) use ($existingScansByDetail): bool {
            return array_key_exists($detailId, $existingScansByDetail);
        }));

        if (count($alreadyScannedDetailIds) > 0) {
            $alreadyScannedPropertyNumbers = array_values(array_filter(array_map(function (int $detailId) use ($mappedByDetail): ?string {
                $value = $mappedByDetail[$detailId]['property_number'] ?? null;
                return is_string($value) ? $value : null;
            }, $alreadyScannedDetailIds)));

            return response()->json([
                'message' => 'Some selected property numbers are already scanned in this cycle.',
                'already_scanned_inventory_detail_ids' => $alreadyScannedDetailIds,
                'already_scanned_property_numbers' => $alreadyScannedPropertyNumbers,
            ], 409);
        }

        $createdScans = [];
        $isBulk = count($requestedDetailIds) > 1;

        try {
            DB::transaction(function () use (
                &$createdScans,
                $cycle,
                $requestedDetailIds,
                $mappedByDetail,
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

                    $createdScans[] = $scan;

                    if (! $isBulk) {
                        $this->createAuditEntry(
                            (int) $cycle->id,
                            'scan_created',
                            $user,
                            [
                                'rpc_type' => $row['rpc_type'] ?? null,
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
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                return response()->json([
                    'message' => 'One or more selected property numbers are already scanned in this cycle.',
                ], 409);
            }

            throw $e;
        }

        // If bulk create, emit a single grouped audit entry to reduce noise
        if ($isBulk && count($createdScans) > 0) {
            $propertyNumbers = array_values(array_filter(array_map(fn ($s) => $s->property_number ?? null, $createdScans)));
            $inventoryDetailIds = array_values(array_filter(array_map(fn ($s) => $s->inventory_detail_id ?? null, $createdScans)));
            $rpcTypes = array_values(array_filter(array_unique(array_map(fn ($s) => $s->rpc_type ?? null, $createdScans))));

            $this->createAuditEntry(
                (int) $cycle->id,
                'scan_created_bulk',
                $user,
                [
                    'total' => count($createdScans),
                    'created' => count($createdScans),
                    'property_numbers' => $propertyNumbers,
                    'inventory_detail_ids' => $inventoryDetailIds,
                    'rpc_types' => $rpcTypes,
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        }

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
                'remarks' => $scan?->remarks ?? ($scan ? null : 'not found'),
                'accountable_officer' => $mapped['accountable_officer'] ?? '-',
                'department' => $mapped['department_name'] ?? '-',
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

    public function auditLogs(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin();
        RpcInventoryCycle::findOrFail($id);

        $perPage = max(10, min(200, (int) $request->query('per_page', 100)));

        $query = RpcInventoryAuditLog::query()
            ->where('rpc_inventory_cycle_id', $id)
            ->with([
                'performedBy:UserID,FullName',
                    'scan:id,property_number,inventory_detail_id,condition_id',
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

        if ($roleName !== self::ROLE_ADMIN) {
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
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
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
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
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

        return [
            'property_id' => (int) $property->PropertyID,
            'inventory_detail_id' => (int) ($property->InventoryDetailID ?? 0),
            'source_dr_detail_id' => $detail?->SourceDRDetailID ? (int) $detail->SourceDRDetailID : null,
            'source_iar_detail_id' => $detail?->SourceIARDetailID ? (int) $detail->SourceIARDetailID : null,
            'property_classification_id' => $classificationId,
            'property_type_name' => $propertyType?->PropertyName,
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

        return [
            'property_id' => $latestAssigned ? (int) $latestAssigned->PropertyID : null,
            'inventory_detail_id' => (int) ($detail->InventoryDetailID ?? 0),
            'source_dr_detail_id' => $detail->SourceDRDetailID ? (int) $detail->SourceDRDetailID : null,
            'source_iar_detail_id' => $detail->SourceIARDetailID ? (int) $detail->SourceIARDetailID : null,
            'property_classification_id' => $classificationId,
            'property_type_name' => $propertyType?->PropertyName,
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
        $seedDetailId = (int) ($seedMapped['inventory_detail_id'] ?? 0);
        $seedRpcType = (string) ($seedMapped['rpc_type'] ?? '');
        $seedKey = $this->buildScanGroupingKey($seedMapped);
        $seedRelaxedKey = $this->buildScanRelaxedGroupingKey($seedMapped);
        $seedSourceDrDetailId = (int) ($seedMapped['source_dr_detail_id'] ?? 0);
        $seedSourceIarDetailId = (int) ($seedMapped['source_iar_detail_id'] ?? 0);

        $allMappedByDetail = [];

        $assignedRows = AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->get();

        foreach ($assignedRows as $assigned) {
            $mapped = $this->mapAssignedProperty($assigned);
            $inventoryDetailId = (int) ($mapped['inventory_detail_id'] ?? 0);

            if ($inventoryDetailId <= 0) {
                continue;
            }

            if ((string) ($mapped['rpc_type'] ?? '') !== $seedRpcType) {
                continue;
            }

            $allMappedByDetail[$inventoryDetailId] = $mapped;
        }

        $inventoryDetails = PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
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

        foreach ($inventoryDetails as $detail) {
            $mapped = $this->mapInventoryDetail($detail);
            $inventoryDetailId = (int) ($mapped['inventory_detail_id'] ?? 0);

            if ($inventoryDetailId <= 0 || isset($allMappedByDetail[$inventoryDetailId])) {
                continue;
            }

            if ((string) ($mapped['rpc_type'] ?? '') !== $seedRpcType) {
                continue;
            }

            $allMappedByDetail[$inventoryDetailId] = $mapped;
        }

        if ($seedDetailId > 0 && !array_key_exists($seedDetailId, $allMappedByDetail)) {
            $allMappedByDetail[$seedDetailId] = $seedMapped;
        }

        if (count($allMappedByDetail) === 0 && $seedDetailId > 0) {
            $allMappedByDetail[$seedDetailId] = $seedMapped;
        }

        $mappedByDetail = [];

        if ($seedSourceDrDetailId > 0 || $seedSourceIarDetailId > 0) {
            foreach ($allMappedByDetail as $inventoryDetailId => $mapped) {
                $candidateSourceDr = (int) ($mapped['source_dr_detail_id'] ?? 0);
                $candidateSourceIar = (int) ($mapped['source_iar_detail_id'] ?? 0);

                $sameSourceDr = $seedSourceDrDetailId > 0 && $candidateSourceDr === $seedSourceDrDetailId;
                $sameSourceIar = $seedSourceIarDetailId > 0 && $candidateSourceIar === $seedSourceIarDetailId;

                if ($sameSourceDr || $sameSourceIar) {
                    $mappedByDetail[$inventoryDetailId] = $mapped;
                }
            }
        }

        if (count($mappedByDetail) <= 1) {
            $mappedByDetail = [];
            foreach ($allMappedByDetail as $inventoryDetailId => $mapped) {
                if ($this->buildScanGroupingKey($mapped) === $seedKey) {
                    $mappedByDetail[$inventoryDetailId] = $mapped;
                }
            }
        }

        if (count($mappedByDetail) <= 1) {
            $mappedByDetail = [];
            foreach ($allMappedByDetail as $inventoryDetailId => $mapped) {
                if ($this->buildScanRelaxedGroupingKey($mapped) === $seedRelaxedKey) {
                    $mappedByDetail[$inventoryDetailId] = $mapped;
                }
            }
        }

        if (count($mappedByDetail) === 0 && $seedDetailId > 0) {
            $mappedByDetail[$seedDetailId] = $seedMapped;
        }

        $detailIds = array_map(static fn ($value) => (int) $value, array_keys($mappedByDetail));

        $existingScansByDetail = [];
        if (count($detailIds) > 0) {
            $existingScansByDetail = RpcInventoryScan::query()
                ->where('rpc_inventory_cycle_id', $cycleId)
                ->whereIn('inventory_detail_id', $detailIds)
                ->get()
                ->keyBy('inventory_detail_id')
                ->all();
        }

        $orderedDetailIds = $detailIds;
        usort($orderedDetailIds, function (int $a, int $b) use ($mappedByDetail): int {
            $left = (string) ($mappedByDetail[$a]['property_number'] ?? '');
            $right = (string) ($mappedByDetail[$b]['property_number'] ?? '');

            return strnatcasecmp($left, $right);
        });

        $candidates = [];
        foreach ($orderedDetailIds as $detailId) {
            $mapped = $mappedByDetail[$detailId];
            $existingScan = $existingScansByDetail[$detailId] ?? null;

            $candidates[] = [
                'inventory_detail_id' => $detailId,
                'property_id' => isset($mapped['property_id']) && $mapped['property_id'] !== null
                    ? (int) $mapped['property_id']
                    : null,
                'property_number' => $mapped['property_number'] ?? null,
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
            strtolower(trim((string) ($mapped['department_name'] ?? ''))),
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
            (string) ((int) ($mapped['department_id'] ?? 0)),
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
                strtolower(trim((string) ($row['department'] ?? ''))),
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

            $remarksList = array_keys($group['remarks']);
            $finalRow['remarks'] = count($remarksList) > 0
                ? $remarksList[0]
                : '-';

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

        // Check if cycle is closed
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

        $results = [];
        $createdCount = 0;
        $duplicateCount = 0;
        $errorCount = 0;
        $syncedPropertyNumbers = [];

        DB::transaction(function () use (
            &$results,
            &$createdCount,
            &$duplicateCount,
            &$errorCount,
            &$syncedPropertyNumbers,
            $cycle,
            $validated,
            $user,
            $source
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
                    // Check for duplicate by client_uuid if provided
                    if ($clientUuid) {
                        $existing = RpcInventoryScan::where('rpc_inventory_cycle_id', (int) $cycle->id)
                            ->where('client_uuid', $clientUuid)
                            ->first();

                        if ($existing) {
                            $results[] = [
                                'client_uuid' => $clientUuid,
                                'status' => 'duplicate',
                                'message' => 'Scan already recorded with this client UUID in this cycle',
                                'scan_id' => (int) $existing->id,
                            ];
                            $duplicateCount++;
                            continue;
                        }
                    }

                    // Resolve inventory detail
                    if (!$inventoryDetailId && !$propertyId) {
                        $results[] = [
                            'client_uuid' => $clientUuid,
                            'status' => 'error',
                            'message' => 'Inventory Detail ID or Property ID is required',
                        ];
                        $errorCount++;
                        continue;
                    }

                    $inventoryDetail = null;
                    if ($inventoryDetailId) {
                        $inventoryDetail = $this->resolveInventoryDetailById($inventoryDetailId);
                    } elseif ($propertyId) {
                        $assignedProperty = $this->resolveAssignedProperty($propertyId);
                        if ($assignedProperty) {
                            $inventoryDetail = $assignedProperty->inventoryDetail;
                        }
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

                    // Map the inventory detail
                    $mapped = $this->mapInventoryDetail($inventoryDetail);

                    // Check if already scanned in this cycle (regardless of client_uuid)
                    $alreadyScanned = RpcInventoryScan::where('rpc_inventory_cycle_id', (int) $cycle->id)
                        ->where('inventory_detail_id', (int) $inventoryDetail->InventoryDetailID)
                        ->first();

                    if ($alreadyScanned) {
                        $results[] = [
                            'client_uuid' => $clientUuid,
                            'status' => 'duplicate',
                            'message' => 'Inventory detail already scanned in this cycle',
                            'scan_id' => (int) $alreadyScanned->id,
                        ];
                        $duplicateCount++;
                        continue;
                    }

                    // Create the scan
                    $scan = RpcInventoryScan::create([
                        'rpc_inventory_cycle_id' => (int) $cycle->id,
                        'inventory_detail_id' => (int) $inventoryDetail->InventoryDetailID,
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

                    $this->createAuditEntry(
                        (int) $cycle->id,
                        'scan_synced',
                        $user,
                        [
                            'sync_source' => $source,
                            'rpc_type' => (string) ($mapped['rpc_type'] ?? ''),
                            'client_uuid' => $clientUuid,
                            'device_id' => $deviceId,
                            'status' => 'created',
                            'inventory_detail_id' => (int) $inventoryDetail->InventoryDetailID,
                            'property_number' => $scan->property_number,
                            'property_id' => $scan->property_id,
                            'condition_id' => $conditionId,
                            'remarks' => $remarks,
                        ],
                        $scan,
                        $conditionId
                    );

                    if ($scan->property_number) {
                        $syncedPropertyNumbers[] = (string) $scan->property_number;
                    }

                    $results[] = [
                        'client_uuid' => $clientUuid,
                        'status' => 'created',
                        'message' => 'Scan recorded successfully',
                        'scan_id' => (int) $scan->id,
                    ];
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

        // Derive a clear action/status for the batch sync so the audit trail reflects result
        $batchStatus = 'completed';
        if ($errorCount > 0) {
            $batchStatus = ($createdCount > 0 || $duplicateCount > 0) ? 'partially_completed' : 'failed';
        }

        $this->createAuditEntry(
            (int) $cycle->id,
            'sync_batch_' . $batchStatus,
            $user,
            [
                'source' => $source,
                'property_number' => 'Batched',
                'total' => count($validated['scans']),
                'created' => $createdCount,
                'duplicates' => $duplicateCount,
                'errors' => $errorCount,
                'property_numbers' => array_values(array_unique(array_filter($syncedPropertyNumbers))),
                'status' => $batchStatus,
            ]
        );

        return response()->json([
            'message' => "Bulk sync processed: {$createdCount} created, {$duplicateCount} skipped, {$errorCount} errors",
            'summary' => [
                'total' => count($validated['scans']),
                'created' => $createdCount,
                'duplicates' => $duplicateCount,
                'errors' => $errorCount,
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
            'close_reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            $cycle = RpcInventoryCycle::findOrFail($id);

            if ($cycle->isClosed()) {
                return response()->json([
                    'message' => 'This inventory cycle is already closed.',
                ], 409);
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
                    'reopen_count' => $cycle->reopen_count,
                    'max_reopens' => 2,
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
                    'reopen_count' => $cycle->reopen_count,
                    'max_reopens' => 2,
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
