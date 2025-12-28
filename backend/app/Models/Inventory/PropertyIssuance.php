<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class PropertyIssuance extends Model
{
    protected $table = 'propertyissuance';
    protected $primaryKey = 'IssuanceID';
    public $timestamps = false;

    protected $fillable = [
        'IssuedBy',
        'IssuedTo',
        'DateIssued',
        'Remarks',
        'CreatedDate',
    ];

    public function details()
    {
        return $this->hasMany(PropertyIssuanceDetail::class, 'IssuanceID', 'IssuanceID');
    }
}
