<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionReportItem extends Model
{
    protected $table = 'inspection_report_items';
    protected $primaryKey = 'InspectionReportDetailID';

    protected $fillable = [
        'InspectionReportID',
        'DRDetailID',
        'Status',
        'Remarks',
        'NoSerial',
    ];

    protected $casts = [
        'NoSerial' => 'boolean',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(InspectionReport::class, 'InspectionReportID', 'InspectionReportID');
    }

    public function drDetail(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceiptDetail::class, 'DRDetailID', 'DRDetailID');
    }
}
