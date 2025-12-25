<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;

class InspectionAcceptanceReport extends Model
{
    protected $table = 'inspectionacceptancereport';
    protected $primaryKey = 'IARID';
    public $timestamps = false;

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = null;

    protected $fillable = [
        'IARNumber',
        'DRID',
        'InspectedBy',
        'AcceptedBy',
        'DateInspected',
        'DateAccepted',
        'Remarks',
    ];

    protected $casts = [
        'DateInspected' => 'date',
        'DateAccepted' => 'date',
        'CreatedDate' => 'datetime',
    ];

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, 'DRID', 'DRID');
    }

    public function inspectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'InspectedBy', 'UserID');
    }

    public function acceptedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'AcceptedBy', 'UserID');
    }

    public function details(): HasMany
    {
        return $this->hasMany(IARDetail::class, 'IARID', 'IARID');
    }
}
