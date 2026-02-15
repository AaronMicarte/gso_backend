<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\EndUser;
use App\Models\AssetRequest;

class PropertyIssuance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyissuance';
    protected $primaryKey = 'IssuanceID';
    public $timestamps = false;

    protected $fillable = [
        'RequestID',
        'DRID',
        'IssuedBy',
        'IssuerSignature',
        'IssuerSignatureSource',
        'IssuedTo',
        'IssuedToName',
        'DocumentType',
        'DocumentNumber',
        'Status',
        'DateIssued',
        'Remarks',
        'AcknowledgmentSignature',
        'AcknowledgedBy',
        'AcknowledgedAt',
        'AcknowledgmentNotes',
        'CreatedDate',
    ];

    protected $casts = [
        'DateIssued' => 'date',
        'AcknowledgedAt' => 'datetime',
        'CreatedDate' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'DateIssued',
        'AcknowledgedAt',
        'CreatedDate',
        'deleted_at',
    ];

    // Relationships
    public function request(): BelongsTo
    {
        return $this->belongsTo(AssetRequest::class, 'RequestID', 'RequestID');
    }

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Procurement\DeliveryReceipt::class, 'DRID', 'DRID');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IssuedBy', 'UserID');
    }

    public function issuedTo(): BelongsTo
    {
        return $this->belongsTo(EndUser::class, 'IssuedTo', 'EndUserID');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PropertyIssuanceDetail::class, 'IssuanceID', 'IssuanceID');
    }

    // Helper methods
    public function generateDocumentNumber(string $type = 'PAR'): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastNumber = static::where('DocumentType', $type)
            ->whereYear('DateIssued', $year)
            ->whereMonth('DateIssued', $month)
            ->max('DocumentNumber');
        
        if ($lastNumber) {
            $lastSequence = (int)substr($lastNumber, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $type, $year, $month, $newSequence);
    }

    public function getStatusAttribute($value): string
    {
        // Always prefer the actual stored Status column.
        // This system uses: pending_acknowledgment, acknowledged, returned, disposed, etc.
        if ($value !== null) {
            return (string) $value;
        }

        return (string) ($this->attributes['Status'] ?? 'pending_acknowledgment');
    }

    public function getDocumentTypeAttribute(): string
    {
        return $this->attributes['DocumentType'] ?? 'PAR';
    }
}