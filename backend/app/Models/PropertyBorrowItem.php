<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\PropertyReturnItem;

class PropertyBorrowItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyborrowitems';
    protected $primaryKey = 'BorrowItemID';

    protected $fillable = [
        'BorrowID',
        'InventoryDetailID',
        'Quantity',
        'ApprovedQuantity',
        'Remarks',
    ];

    protected $casts = [
        'Quantity' => 'integer',
        'ApprovedQuantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function borrow(): BelongsTo
    {
        return $this->belongsTo(PropertyBorrow::class, 'BorrowID', 'BorrowID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID')->withTrashed();
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(PropertyReturnItem::class, 'BorrowItemID', 'BorrowItemID');
    }
}
