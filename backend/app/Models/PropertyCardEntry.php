<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Inventory\PropertyInventoryDetail;

class PropertyCardEntry extends Model
{
    protected $table = 'property_card_entries';
    protected $primaryKey = 'EntryID';
    public $timestamps = false;

    protected $fillable = [
        'InventoryDetailID',
        'EntryDate',
        'EntryType',
        'ReceiptQty',
        'IssueQty',
        'ReferenceNo',
        'OfficeOfficer',
        'UnitCost',
        'Amount',
        'Remarks',
        'SourceType',
        'SourceID',
        'SourceKey',
        'CreatedBy',
        'CreatedAt',
    ];

    protected $casts = [
        'EntryDate' => 'datetime',
        'UnitCost' => 'float',
        'Amount' => 'float',
        'CreatedAt' => 'datetime',
    ];

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }
}
