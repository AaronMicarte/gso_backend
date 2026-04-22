<?php

namespace App\Http\Controllers;

use App\Models\RsmiReport;
use App\Models\RsmiReportItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RsmiReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->ensureTablesInstalled();
        $user = $this->authorizeGso();

        $startDate = trim((string) $request->query('start_date', ''));
        $endDate = trim((string) $request->query('end_date', ''));

        if ($startDate !== '' || $endDate !== '') {
            if ($startDate === '' || $endDate === '') {
                return response()->json([
                    'message' => 'Both start_date and end_date are required for date range filtering.',
                ], 422);
            }

            try {
                $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Invalid date format. Use YYYY-MM-DD for start_date and end_date.',
                ], 422);
            }

            if ($start->gt($end)) {
                return response()->json([
                    'message' => 'start_date must be less than or equal to end_date.',
                ], 422);
            }

            $this->syncReportsForRange($start->toDateString(), $end->toDateString(), (int) $user->UserID);

            $reports = RsmiReport::query()
                ->withCount('items')
                ->withSum('items', 'quantity_issued')
                ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
                ->orderByDesc('report_date')
                ->get();

            return response()->json($reports);
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $this->syncReportsForPeriod($month, $year, (int) $user->UserID);

        $query = RsmiReport::query()
            ->withCount('items')
            ->withSum('items', 'quantity_issued')
            ->orderByDesc('report_date');

        if ($month >= 1 && $month <= 12) {
            $query->whereMonth('report_date', $month);
        }

        if ($year >= 2000 && $year <= 9999) {
            $query->whereYear('report_date', $year);
        }

        return response()->json($query->get());
    }

    private function syncReportsForRange(string $startDate, string $endDate, int $userId): void
    {
        $issuedDates = $this->issuedDatesForRange($startDate, $endDate);

        foreach ($issuedDates as $issuedDate) {
            $this->syncReportForDate($issuedDate, $userId, null);
        }

        $existingForRange = RsmiReport::withTrashed()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->get();

        foreach ($existingForRange as $report) {
            $dateKey = optional($report->report_date)->toDateString() ?? '';
            if (!in_array($dateKey, $issuedDates, true)) {
                RsmiReportItem::where('rsmi_header_id', (int) $report->id)->delete();
                $report->forceDelete();
            }
        }
    }

    public function show(int $id): JsonResponse
    {
        $this->ensureTablesInstalled();
        $user = $this->authorizeGso();

        $report = RsmiReport::with(['items'])->findOrFail($id);

        $synced = $this->syncReportForDate(
            optional($report->report_date)->toDateString() ?? '',
            (int) $user->UserID,
            null
        );

        if (!$synced) {
            return response()->json([
                'message' => 'RSMI report no longer has issued items for this date.',
            ], 404);
        }

        return response()->json($this->buildReportPayload($synced->fresh(['items'])));
    }

    private function syncReportsForPeriod(int $month, int $year, int $userId): void
    {
        $issuedDates = $this->issuedDatesForPeriod($month, $year);

        foreach ($issuedDates as $issuedDate) {
            $this->syncReportForDate($issuedDate, $userId, null);
        }

        $existingForPeriod = RsmiReport::withTrashed()
            ->whereMonth('report_date', $month)
            ->whereYear('report_date', $year)
            ->get();

        foreach ($existingForPeriod as $report) {
            $dateKey = optional($report->report_date)->toDateString() ?? '';
            if (!in_array($dateKey, $issuedDates, true)) {
                RsmiReportItem::where('rsmi_header_id', (int) $report->id)->delete();
                $report->forceDelete();
            }
        }
    }

    /**
     * @param array{entity_name?:string|null,fund_cluster?:string|null}|null $overrides
     */
    private function syncReportForDate(string $reportDate, int $userId, ?array $overrides): ?RsmiReport
    {
        if ($reportDate === '') {
            return null;
        }

        return DB::transaction(function () use ($reportDate, $userId, $overrides) {
            $existing = RsmiReport::withTrashed()
                ->whereDate('report_date', $reportDate)
                ->lockForUpdate()
                ->first();

            $rows = $this->buildDailyRows($reportDate);

            if (count($rows) === 0) {
                if ($existing) {
                    RsmiReportItem::where('rsmi_header_id', (int) $existing->id)->delete();
                    $existing->forceDelete();
                }

                return null;
            }

            if ($existing && $existing->trashed()) {
                $existing->restore();
            }

            if (!$existing) {
                [$serialNo, $year, $month, $sequence] = $this->generateSerialNo($reportDate);

                $existing = RsmiReport::create([
                    'report_date' => $reportDate,
                    'serial_no' => $serialNo,
                    'serial_year' => $year,
                    'serial_month' => $month,
                    'serial_sequence' => $sequence,
                    'entity_name' => isset($overrides['entity_name']) && $overrides['entity_name'] !== null
                        ? (string) $overrides['entity_name']
                        : (string) config('gso.entity_name', ''),
                    'fund_cluster' => isset($overrides['fund_cluster']) && $overrides['fund_cluster'] !== null
                        ? (string) $overrides['fund_cluster']
                        : (string) config('gso.fund_cluster', ''),
                    'created_by_user_id' => $userId,
                ]);
            } elseif ($overrides) {
                $existing->update([
                    'entity_name' => array_key_exists('entity_name', $overrides) && $overrides['entity_name'] !== null
                        ? (string) $overrides['entity_name']
                        : (string) $existing->entity_name,
                    'fund_cluster' => array_key_exists('fund_cluster', $overrides) && $overrides['fund_cluster'] !== null
                        ? (string) $overrides['fund_cluster']
                        : (string) $existing->fund_cluster,
                ]);
            }

            RsmiReportItem::where('rsmi_header_id', (int) $existing->id)->delete();

            foreach ($rows as $row) {
                RsmiReportItem::create([
                    'rsmi_header_id' => (int) $existing->id,
                    'ris_id' => $row['ris_id'],
                    'ris_no' => $row['ris_no'],
                    'responsibility_center_code' => $row['responsibility_center_code'],
                    'stock_no' => $row['stock_no'],
                    'item_description' => $row['item_description'],
                    'unit' => $row['unit'],
                    'quantity_issued' => $row['quantity_issued'],
                ]);
            }

            return $existing;
        });
    }

    /**
     * @return array<int, string>
     */
    private function issuedDatesForPeriod(int $month, int $year): array
    {
        return DB::table('propertyissuance as pi')
            ->whereNull('pi.deleted_at')
            ->whereMonth('pi.DateIssued', $month)
            ->whereYear('pi.DateIssued', $year)
            ->selectRaw('DATE(pi.DateIssued) as report_date')
            ->distinct()
            ->orderBy('report_date')
            ->pluck('report_date')
            ->map(static fn ($date) => (string) $date)
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function issuedDatesForRange(string $startDate, string $endDate): array
    {
        return DB::table('propertyissuance as pi')
            ->whereNull('pi.deleted_at')
            ->whereDate('pi.DateIssued', '>=', $startDate)
            ->whereDate('pi.DateIssued', '<=', $endDate)
            ->selectRaw('DATE(pi.DateIssued) as report_date')
            ->distinct()
            ->orderBy('report_date')
            ->pluck('report_date')
            ->map(static fn ($date) => (string) $date)
            ->values()
            ->all();
    }

    private function ensureTablesInstalled(): void
    {
        if (!Schema::hasTable('rsmi_header') || !Schema::hasTable('rsmi_items')) {
            abort(response()->json([
                'message' => 'RSMI tables are not installed. Run migrations to create rsmi_header and rsmi_items tables.',
            ], 500));
        }
    }

    private function authorizeGso()
    {
        $user = Auth::user();
        $roleName = $user->userRole->RoleName ?? '';

        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }

        return $user;
    }

    /**
     * @return array<int, array<string, int|string|null>>
     */
    private function buildDailyRows(string $reportDate): array
    {
        $rows = DB::table('propertyissuance as pi')
            ->join('propertyissuancedetail as pid', 'pid.IssuanceID', '=', 'pi.IssuanceID')
            ->leftJoin('requisition_issue_slips as ris', function ($join) {
                $join->on('ris.asset_request_id', '=', 'pi.RequestID')
                    ->whereNull('ris.deleted_at');
            })
            ->leftJoin('requisition_issue_slip_items as rii', function ($join) {
                $join->on('rii.ris_id', '=', 'ris.id')
                    ->on('rii.asset_request_detail_id', '=', 'pid.RequestDetailID')
                    ->whereNull('rii.deleted_at');
            })
            ->leftJoin('propertyinventorydetail as inv', function ($join) {
                $join->on('inv.InventoryDetailID', '=', 'pid.InventoryDetailID')
                    ->whereNull('inv.deleted_at');
            })
            ->whereNull('pi.deleted_at')
            ->whereNull('pid.deleted_at')
            ->whereDate('pi.DateIssued', $reportDate)
            ->selectRaw(
                "
                ris.id as ris_id,
                COALESCE(ris.ris_number, 'N/A') as ris_no,
                COALESCE(ris.responsibility_center_code, '') as responsibility_center_code,
                COALESCE(NULLIF(rii.stock_no, ''), NULLIF(inv.PropertyNumber, ''), 'N/A') as stock_no,
                COALESCE(NULLIF(rii.description, ''), NULLIF(pid.ItemDescription, ''), NULLIF(inv.Description, ''), 'N/A') as item_description,
                COALESCE(NULLIF(rii.unit, ''), NULLIF(pid.Unit, ''), 'N/A') as unit,
                pid.QuantityIssued as quantity_issued
            "
            )
            ->get();

        $aggregated = $rows
            ->groupBy(function ($row) {
                return implode('::', [
                    (string) ($row->ris_id ?? ''),
                    (string) ($row->ris_no ?? 'N/A'),
                    (string) ($row->responsibility_center_code ?? ''),
                    (string) ($row->stock_no ?? 'N/A'),
                    (string) ($row->item_description ?? 'N/A'),
                    (string) ($row->unit ?? 'N/A'),
                ]);
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'ris_id' => $first->ris_id ? (int) $first->ris_id : null,
                    'ris_no' => (string) ($first->ris_no ?? 'N/A'),
                    'responsibility_center_code' => (string) ($first->responsibility_center_code ?? ''),
                    'stock_no' => (string) ($first->stock_no ?? 'N/A'),
                    'item_description' => (string) ($first->item_description ?? 'N/A'),
                    'unit' => (string) ($first->unit ?? 'N/A'),
                    'quantity_issued' => (int) $group->sum(function ($row) {
                        return (int) ($row->quantity_issued ?? 0);
                    }),
                ];
            })
            ->sortBy([
                ['ris_no', 'asc'],
                ['stock_no', 'asc'],
            ])
            ->values();

        return $aggregated->map(function (array $row) {
            return [
                'ris_id' => $row['ris_id'],
                'ris_no' => $row['ris_no'],
                'responsibility_center_code' => $row['responsibility_center_code'],
                'stock_no' => $row['stock_no'],
                'item_description' => $row['item_description'],
                'unit' => $row['unit'],
                'quantity_issued' => $row['quantity_issued'],
            ];
        })->values()->all();
    }

    /**
     * @return array{0:string,1:int,2:int,3:int}
     */
    private function generateSerialNo(string $reportDate): array
    {
        $date = Carbon::parse($reportDate);
        $year = (int) $date->year;
        $month = (int) $date->month;

        $maxSequence = (int) RsmiReport::withTrashed()
            ->where('serial_year', $year)
            ->where('serial_month', $month)
            ->lockForUpdate()
            ->max('serial_sequence');

        $nextSequence = $maxSequence + 1;
        $serialNo = sprintf('%04d-%02d-%04d', $nextSequence, $month, $year);

        return [$serialNo, $year, $month, $nextSequence];
    }

    private function buildReportPayload(RsmiReport $report): array
    {
        $items = $report->items
            ->sortBy([
                ['ris_no', 'asc'],
                ['stock_no', 'asc'],
            ])
            ->values();

        $recapitulation = $items
            ->groupBy(function (RsmiReportItem $item) {
                return (string) $item->stock_no;
            })
            ->map(function ($group, $stockNo) {
                return [
                    'stock_no' => (string) $stockNo,
                    'quantity' => (int) $group->sum('quantity_issued'),
                ];
            })
            ->values()
            ->all();

        return [
            'id' => (int) $report->id,
            'serial_no' => (string) $report->serial_no,
            'report_date' => optional($report->report_date)->toDateString(),
            'entity_name' => (string) ($report->entity_name ?? ''),
            'fund_cluster' => (string) ($report->fund_cluster ?? ''),
            'items' => $items->map(function (RsmiReportItem $item) {
                return [
                    'id' => (int) $item->id,
                    'ris_no' => (string) ($item->ris_no ?? 'N/A'),
                    'responsibility_center_code' => (string) ($item->responsibility_center_code ?? ''),
                    'stock_no' => (string) $item->stock_no,
                    'item_description' => (string) $item->item_description,
                    'unit' => (string) $item->unit,
                    'quantity_issued' => (int) $item->quantity_issued,
                ];
            })->values()->all(),
            'recapitulation' => $recapitulation,
        ];
    }
}
