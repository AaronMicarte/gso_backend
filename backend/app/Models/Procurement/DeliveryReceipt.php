<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Auth\User;

class DeliveryReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'deliveryreceipt';
    protected $primaryKey = 'DRID';
    public $timestamps = false;

    protected $fillable = [
        'DRNumber',
        'SupplierName',
        'PONumber',
        'InvoiceNumber',
        'ReceivedBy',
        'DateReceived',
        'Remarks',
        'CreatedDate',
        'dr_picture'
    ];

    protected $casts = [
        'DateReceived' => 'date',
        'CreatedDate' => 'datetime',
    ];

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ReceivedBy', 'UserID');
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
