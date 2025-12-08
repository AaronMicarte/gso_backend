<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchaseorderstatus';
    protected $primaryKey = 'POStatusID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'StatusName',
    ];
}
