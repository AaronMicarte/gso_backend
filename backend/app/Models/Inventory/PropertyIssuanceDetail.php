<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class PropertyIssuanceDetail extends Model
{
    protected $table = 'propertyissuancedetail';
    protected $primaryKey = 'IssuanceDetailID';
    public $timestamps = false;

    protected $fillable = [
        'IssuanceID',
        'RequestDetailID',
        'InventoryDetailID',
        'ItemDescription',
        'Unit',
        'QuantityIssued',
        'Remarks',
    ];

    public function requestDetail()
    {
        return $this->belongsTo(\App\Models\AssetRequestDetail::class, 'RequestDetailID', 'RequestDetailID');
    }

    public function issuance()
    {
        return $this->belongsTo(PropertyIssuance::class, 'IssuanceID', 'IssuanceID');
    }

    public function inventoryDetail()
    {
        return $this->belongsTo(\App\Models\Inventory\PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }
}
