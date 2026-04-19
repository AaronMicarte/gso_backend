<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function scans()
    {
        return $this->hasMany(RpcInventoryScan::class, 'rpc_inventory_cycle_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'created_by_user_id', 'UserID');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'updated_by_user_id', 'UserID');
    }
}
