<?php

namespace App\Models;

use App\Models\AssetAuditLog;
use App\Models\Auth\User;
use App\Models\Inventory\PropertyInventoryDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyUnserviceableReport extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'property_unserviceable_reports';
    protected $primaryKey = 'id';

    protected $fillable = [
        'report_number',
        'status',
        'remarks',
        'created_by_user_id',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PropertyUnserviceableItem::class, 'unserviceable_report_id', 'id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PropertyUnserviceableAttachment::class, 'unserviceable_report_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'UserID');
    }

    /**
     * Report statuses that block filing a new unserviceable report for the same item.
     */
    public static function activeStatuses(): array
    {
        return [self::STATUS_ACTIVE];
    }

    public static function isActiveStatus(?string $status): bool
    {
        if ($status === null || $status === '') {
            return false;
        }

        return in_array(strtolower($status), self::activeStatuses(), true);
    }

    /**
     * @return array<string, list<string>>
     */
    public static function allowedStatusTransitions(): array
    {
        return [
            self::STATUS_ACTIVE => [self::STATUS_RESOLVED, self::STATUS_CLOSED],
            self::STATUS_RESOLVED => [self::STATUS_CLOSED],
            self::STATUS_CLOSED => [],
        ];
    }

    public function canTransitionTo(string $nextStatus): bool
    {
        $current = strtolower((string) $this->status);
        $next = strtolower($nextStatus);
        $allowed = self::allowedStatusTransitions()[$current] ?? [];

        return in_array($next, $allowed, true);
    }

    public function transitionTo(string $nextStatus): bool
    {
        if (!$this->canTransitionTo($nextStatus)) {
            return false;
        }

        $this->status = strtolower($nextStatus);

        return $this->save();
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::activeStatuses());
    }

    public static function inventoryDetailIsServiceable(PropertyInventoryDetail $detail): bool
    {
        $name = strtolower(trim((string) ($detail->condition?->ConditionName ?? '')));
        if ($name !== '') {
            if (str_contains($name, 'unserviceable')) {
                return false;
            }
            if (str_contains($name, 'under repair') || str_contains($name, 'repair')) {
                return false;
            }

            return str_contains($name, 'serviceable');
        }

        // Fallback when condition relation is missing (ConditionID 1 = Serviceable in legacy data).
        return (int) $detail->ConditionID === 1;
    }

    /**
     * Close out active reports when the asset is serviceable again (repair completed outside report workflow).
     *
     * @return list<int> Report IDs that were transitioned to resolved
     */
    public static function reconcileActiveReportsForServiceableInventory(
        PropertyInventoryDetail $detail,
        int $performedByUserId,
        ?array $auditContext = null
    ): array {
        if (!self::inventoryDetailIsServiceable($detail)) {
            return [];
        }

        return self::resolveActiveReportsForInventoryDetail(
            (int) $detail->InventoryDetailID,
            $performedByUserId,
            array_merge(['reconciliation' => 'inventory_serviceable'], $auditContext ?? [])
        );
    }

    /**
     * Resolve all active unserviceable reports linked to an inventory detail (e.g. after repair).
     *
     * @return list<int> Report IDs that were transitioned to resolved
     */
    public static function resolveActiveReportsForInventoryDetail(int $inventoryDetailId, int $performedByUserId, ?array $auditContext = null): array
    {
        $resolvedReportIds = [];

        $activeItems = PropertyUnserviceableItem::query()
            ->where('inventory_detail_id', $inventoryDetailId)
            ->whereHas('report', fn ($q) => $q->active())
            ->with('report')
            ->get();

        foreach ($activeItems as $item) {
            $report = $item->report;
            if (!$report || !self::isActiveStatus($report->status)) {
                continue;
            }

            if (!$report->transitionTo(self::STATUS_RESOLVED)) {
                continue;
            }

            $resolvedReportIds[] = (int) $report->id;

            AssetAuditLog::createLog(
                'property_unserviceable_report',
                (int) $report->id,
                'unserviceable_report_resolved',
                $performedByUserId,
                array_merge(
                    [
                        'report_number' => $report->report_number,
                        'inventory_detail_id' => $inventoryDetailId,
                        'previous_status' => self::STATUS_ACTIVE,
                        'new_status' => self::STATUS_RESOLVED,
                    ],
                    $auditContext ?? []
                )
            );
        }

        return $resolvedReportIds;
    }

    public static function generateReportNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'UNS-' . $year . '-';

        $latest = self::withTrashed()
            ->where('report_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('report_number');

        $next = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $sequence = (int) end($parts);
            $next = $sequence + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
