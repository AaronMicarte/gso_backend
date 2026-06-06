<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RpcInventoryCycleSnapshot extends Model
{
    protected $table = 'rpc_inventory_cycle_snapshots';

    protected $fillable = [
        'rpc_inventory_cycle_id',
        'snapshot_json',
        'snapshot_hash',
        'created_by_user_id',
    ];

    protected $casts = [
        'snapshot_json' => 'array',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(RpcInventoryCycle::class, 'rpc_inventory_cycle_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'UserID');
    }
}
