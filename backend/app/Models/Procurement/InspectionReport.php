<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;

class InspectionReport extends Model
{
    use SoftDeletes;

    protected $table = 'inspection_reports';
    protected $primaryKey = 'InspectionReportID';

    protected $fillable = [
        'DRID',
        'InspectedBy',
        'InspectorName',
        'DateInspected',
        'Status',
        'Remarks',
        'CompletedAt',
        'IPAddress',
        'UserAgent',
    ];

    protected $casts = [
        'DateInspected' => 'date',
        'CompletedAt' => 'datetime',
    ];

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, 'DRID', 'DRID');
    }

    public function inspectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'InspectedBy', 'UserID');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InspectionReportItem::class, 'InspectionReportID', 'InspectionReportID');
    }
}
