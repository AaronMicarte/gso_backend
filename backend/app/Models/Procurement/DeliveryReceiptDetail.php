<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MasterData\PropertyType;

class DeliveryReceiptDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'deliveryreceiptdetail';
    protected $primaryKey = 'DRDetailID';
    public $timestamps = false;

    protected $fillable = [
        'DRID',
        'ItemDescription',
        'PropertyTypeID',
        'QuantityDelivered',
        'UnitCost',
        'TotalCost'
    ];

    protected $casts = [
        'QuantityDelivered' => 'integer',
        'UnitCost' => 'decimal:2',
        'TotalCost' => 'decimal:2',
    ];

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, 'DRID', 'DRID');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'PropertyTypeID', 'PropertyTypeID');
    }
}
