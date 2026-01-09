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
        'InventoryID',
        'PropertyTypeID',
        'PropertyNumber',
        'ConditionID',
        'Brand',
        'ModelNumber',
        'SerialNumber',
        'Description',
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
    ];

    protected $appends = ['ItemDescription'];

    public function getItemDescriptionAttribute()
    {
        return $this->Description;
    }

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

    public function propertyStatus()
    {
        return $this->belongsTo(\App\Models\MasterData\PropertyStatus::class, 'PropertyStatusID', 'PropertyStatusID');
    }

    public function condition()
    {
        return $this->belongsTo(\App\Models\MasterData\Condition::class, 'ConditionID', 'ConditionID');
    }

    public function drDetail()
    {
        return $this->belongsTo(\App\Models\Procurement\DeliveryReceiptDetail::class, 'SourceDRDetailID', 'DRDetailID');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(\App\Models\MasterData\UnitOfMeasure::class, 'UnitOfMeasureID', 'UnitOfMeasureID');
    }

    public function assetAssignments()
    {
        return $this->hasMany(\App\Models\AssetAssignment::class, 'PropertyInventoryDetailID', 'InventoryDetailID');
    }
}
