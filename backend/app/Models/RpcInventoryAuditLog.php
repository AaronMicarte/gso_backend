<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RpcInventoryAuditLog extends Model
{
    use HasFactory;

    protected $table = 'rpc_inventory_audit_logs';
    protected $primaryKey = 'AuditID';

    protected $fillable = [
        'rpc_inventory_cycle_id',
        'rpc_inventory_scan_id',
        'action',
        'source',
        'performed_by_user_id',
        'performed_by_device_id',
        'inventory_detail_id',
        'property_id',
        'property_number',
        'condition_id',
        'previous_condition_id',
        'previous_remarks',
        'new_remarks',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'inventory_detail_id' => 'integer',
        'property_id' => 'integer',
        'condition_id' => 'integer',
        'previous_condition_id' => 'integer',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(RpcInventoryCycle::class, 'rpc_inventory_cycle_id', 'id');
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(RpcInventoryScan::class, 'rpc_inventory_scan_id', 'id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id', 'UserID');
    }

    public static function createLog(array $payload): self
    {
        return self::create($payload);
    }
}
