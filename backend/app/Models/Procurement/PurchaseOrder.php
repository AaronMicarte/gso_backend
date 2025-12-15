<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterData\Supplier;
use App\Models\Auth\User;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchaseorder';
    protected $primaryKey = 'PurchaseOrderID';
    public $timestamps = false;

    protected $fillable = [
        'PONumber',
        'PurchaseRequestID',
        'SupplierID',
        'POStatusID',
        'PODate',
        'ModeOfProcurement',
        'PlaceOfDelivery',
        'DateOfDelivery',
        'DeliveryTerm',
        'PaymentTerm',
        'TotalAmount',
        'Remarks',
        'CreatedBy',
        'CreatedDate',
        'UpdatedDate'
    ];

    protected $casts = [
        'PODate' => 'date',
        'CreatedDate' => 'datetime',
        'UpdatedDate' => 'datetime',
        'TotalAmount' => 'decimal:2'
    ];

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'PurchaseOrderID', 'PurchaseOrderID');
    }

    public function status()
    {
        return $this->belongsTo(PurchaseOrderStatus::class, 'POStatusID', 'POStatusID');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'PurchaseRequestID', 'PurchaseRequestID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'CreatedBy', 'UserID');
    }
}
