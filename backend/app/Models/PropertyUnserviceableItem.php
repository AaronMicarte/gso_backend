<?php

namespace App\Models;

use App\Models\Inventory\PropertyInventoryDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyUnserviceableItem extends Model
{
    use HasFactory;

    protected $table = 'property_unserviceable_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'unserviceable_report_id',
        'inventory_detail_id',
        'property_number',
        'item_description',
        'remarks',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(PropertyUnserviceableReport::class, 'unserviceable_report_id', 'id');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'inventory_detail_id', 'InventoryDetailID')->withTrashed();
    }
}
