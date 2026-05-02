<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpcInventoryScan extends Model
{
    protected $table = 'rpc_inventory_scans';

    protected $fillable = [
        'rpc_inventory_cycle_id',
        'inventory_detail_id',
        'property_id',
        'rpc_type',
        'property_classification_id',
        'condition_id',
        'scanned_at',
        'scanned_by_user_id',
        'end_user_id',
        'department_id',
        'article',
        'description',
        'property_number',
        'unit_of_measure',
        'unit_value',
        'quantity_per_card',
        'quantity_per_count',
        'remarks',
        'accountable_officer',
        'client_uuid',
        'device_id',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'unit_value' => 'decimal:2',
    ];

    public function cycle()
    {
        return $this->belongsTo(RpcInventoryCycle::class, 'rpc_inventory_cycle_id', 'id');
    }

    public function condition()
    {
        return $this->belongsTo(\App\Models\MasterData\Condition::class, 'condition_id', 'ConditionID');
    }

    public function scannedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'scanned_by_user_id', 'UserID');
    }
}
