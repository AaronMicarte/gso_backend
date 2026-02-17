<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Inventory\PropertyInventoryDetail;

class PropertyTransferItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertytransferdetails';
    protected $primaryKey = 'TransferItemID';

    protected $fillable = [
        'TransferID',
        'InventoryDetailID',
        'Quantity',
        'Remarks',
    ];

    protected $casts = [
        'Quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(PropertyTransfer::class, 'TransferID', 'TransferID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID')->withTrashed();
    }
}
