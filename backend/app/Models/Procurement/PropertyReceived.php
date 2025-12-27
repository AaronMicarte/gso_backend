<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyReceived extends Model
{
    use HasFactory;

    protected $table = 'propertyreceived';
    protected $primaryKey = 'ReceivedID';
    public $timestamps = false;

    protected $fillable = [
        'IARID',
        'ReceivedBy',
        'ReleasedBy',
        'DateReceived',
        'TotalCost',
        'Remarks'
    ];
}