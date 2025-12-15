<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'purchaseorderdetail';
    protected $primaryKey = 'PurchaseOrderDetailID';
    public $timestamps = false;

    protected $fillable = [
        'PurchaseOrderID',
        'ItemDescription',
        'Quantity',
        'UnitCost',
        'TotalCost',
        'Remarks'
    ];

    protected $casts = [
        'UnitCost' => 'decimal:2',
        'TotalCost' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'PurchaseOrderID', 'PurchaseOrderID');
    }
}
