<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RpcInventoryCycle extends Model
{
    use SoftDeletes;

    protected $table = 'rpc_inventory_cycles';

    protected $fillable = [
        'cycle_label',
        'start_year',
        'end_year',
        'status',
        'started_at',
        'closed_at',
        'closed_by_user_id',
        'close_reason',
        'reopened_by_user_id',
        'reopened_at',
        'reopen_reason',
        'reopen_count',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'reopen_count' => 'integer',
    ];

    protected $appends = [
        'can_reopen',
        'is_permanently_closed',
    ];

    public function scans()
    {
        return $this->hasMany(RpcInventoryScan::class, 'rpc_inventory_cycle_id', 'id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(RpcInventoryAuditLog::class, 'rpc_inventory_cycle_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'created_by_user_id', 'UserID');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'updated_by_user_id', 'UserID');
    }

    public function closedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'closed_by_user_id', 'UserID');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'reopened_by_user_id', 'UserID');
    }

    /**
     * Check if cycle is closed (locked).
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if cycle is permanently locked (reopened twice and closed again).
     */
    public function isPermanentlyClosed(): bool
    {
        return $this->isClosed() && $this->reopen_count >= 2;
    }

    /**
     * Check if cycle can be reopened.
     */
    public function canReopen(): bool
    {
        return $this->isClosed() && $this->reopen_count < 2;
    }

    public function getCanReopenAttribute(): bool
    {
        return $this->canReopen();
    }

    public function getIsPermanentlyClosedAttribute(): bool
    {
        return $this->isPermanentlyClosed();
    }

    /**
     * Get closure summary for display.
     */
    public function getClosureSummary(): array
    {
        return [
            'status' => $this->status,
            'closed_by' => $this->closedBy?->FullName,
            'closed_at' => $this->closed_at,
            'close_reason' => $this->close_reason,
            'reopened_by' => $this->reopenedBy?->FullName,
            'reopened_at' => $this->reopened_at,
            'reopen_reason' => $this->reopen_reason,
            'reopen_count' => $this->reopen_count,
            'can_reopen' => $this->canReopen(),
            'is_permanently_closed' => $this->isPermanentlyClosed(),
        ];
    }
}
