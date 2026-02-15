<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MasterData\PropertyType;
use App\Models\MasterData\Condition;

class IARDetail extends Model
{
    protected $table = 'iardetail';
    protected $primaryKey = 'IARDetailID';
    public $timestamps = false;

    protected $fillable = [
        'IARID',
        'DRDetailID',
        'PropertyTypeID',
        'ConditionID',
        'QuantityAccepted',
        'QuantityRejected',
        'VerifiedBrand',
        'VerifiedModel',
        'VerifiedSerial',
        'NoSerial',
        'Remarks',
    ];

    protected $casts = [
        'QuantityAccepted' => 'integer',
        'QuantityRejected' => 'integer',
        'NoSerial' => 'boolean',
    ];

    public function iar(): BelongsTo
    {
        return $this->belongsTo(InspectionAcceptanceReport::class, 'IARID', 'IARID');
    }

    public function drDetail(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceiptDetail::class, 'DRDetailID', 'DRDetailID');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'PropertyTypeID', 'PropertyTypeID');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'ConditionID', 'ConditionID');
    }
}
