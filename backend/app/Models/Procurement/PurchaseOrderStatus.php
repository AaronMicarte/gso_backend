<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderStatus extends Model
{
    use HasFactory;

    protected $table = 'purchaseorderstatus';
    protected $primaryKey = 'POStatusID';
    public $timestamps = false;

    protected $fillable = [
        'StatusName'
    ];
}
