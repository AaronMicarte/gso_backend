<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Inventory\PropertyInventoryDetail;

class PropertyIssuanceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyissuancedetail';
    protected $primaryKey = 'IssuanceDetailID';

    protected $fillable = [
        'IssuanceID',
        'RequestDetailID',
        'InventoryDetailID',
        'ItemDescription',
        'Unit',
        'QuantityIssued',
        'Remarks',
    ];

    protected $casts = [
        'QuantityIssued' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function issuance(): BelongsTo
    {
        return $this->belongsTo(PropertyIssuance::class, 'IssuanceID', 'IssuanceID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }

    public function requestDetail(): BelongsTo
    {
        return $this->belongsTo(AssetRequestDetail::class, 'RequestDetailID', 'RequestDetailID');
    }
}