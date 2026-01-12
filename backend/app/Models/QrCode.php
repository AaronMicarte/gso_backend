<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'qrcode';
    protected $primaryKey = 'QRCodeID';
    public $timestamps = false;

    protected $fillable = [
        'PropertyID',
        'DepartmentID',
        'InventoryDetailID',
        'QRCodeData',
        'DateGenerated',
    ];

    public function inventoryDetail()
    {
        return $this->belongsTo(\App\Models\Inventory\PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }

    public function property()
    {
        return $this->belongsTo(\App\Models\AssignedProperty::class, 'PropertyID', 'PropertyID');
    }
}
