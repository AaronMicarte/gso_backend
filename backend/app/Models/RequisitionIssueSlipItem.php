<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequisitionIssueSlipItem extends Model
{
    use SoftDeletes;

    protected $table = 'requisition_issue_slip_items';

    protected $fillable = [
        'ris_id',
        'asset_request_detail_id',
        'inventory_detail_id',
        'stock_no',
        'unit',
        'description',
        'quantity_requested',
        'stock_available',
        'quantity_issued',
        'issue_remarks',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'stock_available' => 'boolean',
        'quantity_issued' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ris(): BelongsTo
    {
        return $this->belongsTo(RequisitionIssueSlip::class, 'ris_id', 'id');
    }

    public function requestDetail(): BelongsTo
    {
        return $this->belongsTo(AssetRequestDetail::class, 'asset_request_detail_id', 'RequestDetailID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\PropertyInventoryDetail::class, 'inventory_detail_id', 'InventoryDetailID');
    }

    public function issuanceDetails(): HasMany
    {
        return $this->hasMany(PropertyIssuanceDetail::class, 'ris_item_id', 'id');
    }
}
