<?php

namespace App\Models;

use App\Models\Inventory\PropertyInventoryDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRlsddpItem extends Model
{
    use HasFactory;

    protected $table = 'property_rlsddp_items';
    protected $primaryKey = 'RlsddpItemID';

    protected $fillable = [
        'RlsddpID',
        'InventoryDetailID',
        'PropertyNumber',
        'ParNumber',
        'AcquisitionCost',
        'Description',
        'Remarks',
    ];

    protected $casts = [
        'AcquisitionCost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rlsddpReport(): BelongsTo
    {
        return $this->belongsTo(PropertyRlsddpReport::class, 'RlsddpID', 'RlsddpID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }
}
