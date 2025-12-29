<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyInventoryDetail extends Model
{
    use SoftDeletes;

    protected $table = 'propertyinventorydetail';
    protected $primaryKey = 'InventoryDetailID';
    public $timestamps = false;

    protected $fillable = [
        'InventoryDetailID',
        'InventoryID',
        'PropertyTypeID',
        'ConditionID',
        'Brand',
        'ModelNumber',
        'SerialNumber',
        'PropertyNumber',
        'Quantity',
        'QuantityAvailable',
        'PropertyStatusID',
        'UnitOfMeasureID',
        'UnitCost',
        'TotalCost',
        'SourceIARDetailID',
        'SourceDRDetailID',
        'Remarks',
        'CreatedDate',
        'UpdatedDate',
    ];

    public function inventory()
    {
        return $this->belongsTo(PropertyInventory::class, 'InventoryID', 'InventoryID');
    }

    public function qrCode()
    {
        return $this->hasOne(\App\Models\QrCode::class, 'InventoryDetailID', 'InventoryDetailID');
    }

    public function propertyType()
    {
        return $this->belongsTo(\App\Models\MasterData\PropertyType::class, 'PropertyTypeID', 'PropertyTypeID');
    }

    public function drDetail()
    {
        return $this->belongsTo(\App\Models\Procurement\DeliveryReceiptDetail::class, 'SourceDRDetailID', 'DRDetailID');
    }
}
