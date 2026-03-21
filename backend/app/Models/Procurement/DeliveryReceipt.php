<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Auth\User;
use App\Models\MasterData\Supplier;

class DeliveryReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'deliveryreceipt';
    protected $primaryKey = 'DRID';
    public $timestamps = false;

    protected $fillable = [
        'DRNumber',
        'IARNumber',
        'SupplierID',
        'RequestedBy',
        'PRNumber',
        'PONumber',
        'InvoiceNumber',
        'ReceivedBy',
        'InspectedBy',
        'DateReceived',
        'DateInspected',
        'Remarks',
        'CreatedDate',
        'dr_picture',
        'InspectionStatus',
        'InspectionCompletedAt'
    ];

    protected $casts = [
        'DateReceived' => 'date',
        'DateInspected' => 'date',
        'CreatedDate' => 'datetime',
        'InspectionCompletedAt' => 'datetime',
        'SupplierID' => 'integer',
    ];

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ReceivedBy', 'UserID');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'RequestedBy', 'UserID');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DeliveryReceiptDetail::class, 'DRID', 'DRID');
    }

    public function iar(): HasOne
    {
        return $this->hasOne(InspectionAcceptanceReport::class, 'DRID', 'DRID');
    }

}
