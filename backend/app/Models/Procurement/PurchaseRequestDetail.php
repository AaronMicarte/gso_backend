<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'purchaserequestdetail';
    protected $primaryKey = 'PRDetailID';
    public $timestamps = false;

    protected $fillable = [
        'PurchaseRequestID',
        'ItemDescription',
        'UnitOfMeasureID',
        'QuantityRequested',
        'EstimatedUnitCost',
        'EstimatedTotalCost',
        'Remarks'
    ];

    // Map model attributes to database columns if needed, or just use the DB column names in the controller.
    // Since we want to keep the frontend consistent (Quantity, UnitCost), we can use accessors/mutators or just update the controller.
    // Updating the controller is cleaner.


    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'PurchaseRequestID', 'PurchaseRequestID');
    }
}
