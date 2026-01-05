<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetRequestDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assetrequestdetail';
    protected $primaryKey = 'RequestDetailID';

    protected $fillable = [
        'RequestID',
        'PropertyInventoryDetailID',
        'ItemDescription',
        'Unit',
        'QuantityRequested',
        'Purpose',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssetRequest::class, 'RequestID', 'RequestID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'PropertyInventoryDetailID', 'InventoryDetailID');
    }
}
