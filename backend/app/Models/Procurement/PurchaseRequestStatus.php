<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestStatus extends Model
{
    use HasFactory;

    protected $table = 'purchaserequeststatus';
    protected $primaryKey = 'PRStatusID';
    public $timestamps = false;

    protected $fillable = [
        'StatusName'
    ];
}
