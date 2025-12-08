<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequestStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchaserequeststatus';
    protected $primaryKey = 'PRStatusID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'StatusName',
    ];
}
