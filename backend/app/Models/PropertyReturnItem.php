<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Condition;

class PropertyReturnItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyreturnitems';
    protected $primaryKey = 'ReturnItemID';

    protected $fillable = [
        'ReturnID',
        'BorrowItemID',
        'InventoryDetailID',
        'QuantityReturned',
        'ConditionID',
        'Remarks',
    ];

    protected $casts = [
        'QuantityReturned' => 'integer',
        'ConditionID' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function returnRecord(): BelongsTo
    {
        return $this->belongsTo(PropertyReturn::class, 'ReturnID', 'ReturnID');
    }

    public function borrowItem(): BelongsTo
    {
        return $this->belongsTo(PropertyBorrowItem::class, 'BorrowItemID', 'BorrowItemID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID')->withTrashed();
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'ConditionID', 'ConditionID');
    }
}
