<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;
use App\Services\PropertyCardLedgerService;

class AssetAuditLog extends Model
{
    use HasFactory;

    protected $table = 'asset_audit_logs';
    protected $primaryKey = 'AuditID';
    public $timestamps = false;

    protected $fillable = [
        'EntityType',
        'EntityID',
        'Action',
        'PerformedBy',
        'Details',
        'IPAddress',
        'UserAgent',
        'CreatedAt',
    ];

    protected $casts = [
        'CreatedAt' => 'datetime',
        'Details' => 'array',
    ];

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'PerformedBy', 'UserID');
    }

    /**
     * Create audit log entry
     */
    public static function createLog(
        string $entityType,
        int $entityId,
        string $action,
        int $performedBy,
        ?array $details = null
    ): self {
        $log = self::create([
            'EntityType' => $entityType,
            'EntityID' => $entityId,
            'Action' => $action,
            'PerformedBy' => $performedBy,
            'Details' => $details,
            'IPAddress' => request()->ip(),
            'UserAgent' => request()->userAgent(),
            'CreatedAt' => now(),
        ]);

        if ($entityType === 'inventory_detail') {
            try {
                app(PropertyCardLedgerService::class)->syncForInventoryDetail($entityId);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $log;
    }
}
