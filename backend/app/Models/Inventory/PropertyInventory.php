<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyInventory extends Model
{
    use SoftDeletes;

    protected $table = 'propertyinventory';
    protected $primaryKey = 'InventoryID';
    public $timestamps = false;

    protected $fillable = [
        'InventoryID',
        'InventoryNumber',
        'IARID',
        'DateReceived',
        'Remarks',
        'CreatedDate',
        'UpdatedDate',
    ];

    // Keep frontend compatibility: expose DateAcquired (virtual) mapped to DateReceived
    protected $appends = ['DateAcquired', 'TotalItems', 'TotalValue'];

    public function getDateAcquiredAttribute()
    {
        // Prefer DateReceived if present, else fallback to CreatedDate
        return $this->attributes['DateReceived'] ?? $this->attributes['CreatedDate'] ?? null;
    }

    public function getTotalItemsAttribute()
    {
        return $this->details->sum('Quantity');
    }

    public function getTotalValueAttribute()
    {
        return $this->details->sum('TotalCost');
    }

    public function details()
    {
        return $this->hasMany(PropertyInventoryDetail::class, 'InventoryID', 'InventoryID');
    }

    public function iar()
    {
        return $this->belongsTo(\App\Models\Procurement\InspectionAcceptanceReport::class, 'IARID', 'IARID');
    }
}
