<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsmiReportItem extends Model
{
    protected $table = 'rsmi_items';

    protected $fillable = [
        'rsmi_header_id',
        'ris_id',
        'ris_no',
        'responsibility_center_code',
        'stock_no',
        'item_description',
        'unit',
        'quantity_issued',
    ];

    protected $casts = [
        'quantity_issued' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(RsmiReport::class, 'rsmi_header_id', 'id');
    }

    public function ris(): BelongsTo
    {
        return $this->belongsTo(RequisitionIssueSlip::class, 'ris_id', 'id');
    }
}
