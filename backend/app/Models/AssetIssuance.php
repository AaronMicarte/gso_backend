<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetIssuance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_issuance';
    protected $primaryKey = 'IssuanceID';

    protected $fillable = [
        'RequestID',
        'DocumentType',
        'DocumentNumber',
        'PropertyInventoryDetailID',
        'QuantityIssued',
        'IssuedTo',
        'IssuedBy',
        'IssuanceDate',
        'Status',
        'IssuanceNotes',
        'AcknowledgedBy',
        'AcknowledgedAt',
        'AcknowledgmentSignature',
        'AcknowledgmentNotes',
    ];

    protected $casts = [
        'IssuanceDate' => 'datetime',
        'AcknowledgedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssetRequest::class, 'RequestID', 'RequestID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'PropertyInventoryDetailID', 'InventoryDetailID');
    }

    public function issuedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IssuedTo', 'UserID');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IssuedBy', 'UserID');
    }

    public function acknowledger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'AcknowledgedBy', 'UserID');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AssetAuditLog::class, 'EntityID', 'IssuanceID')
            ->where('EntityType', 'asset_issuance');
    }

    /**
     * Generate document number based on type
     */
    public static function generateDocumentNumber(string $type): string
    {
        $year = date('Y');
        $prefix = strtoupper($type) . "-{$year}-";
        
        $lastIssuance = self::where('DocumentNumber', 'like', $prefix . '%')
            ->orderBy('IssuanceID', 'desc')
            ->first();

        if ($lastIssuance) {
            $lastNumber = (int) substr($lastIssuance->DocumentNumber, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if issuance can be acknowledged
     */
    public function canBeAcknowledged(): bool
    {
        return $this->Status === 'pending_acknowledgment';
    }

    /**
     * Get the full signature URL
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (!$this->AcknowledgmentSignature) {
            return null;
        }

        return url('storage/' . $this->AcknowledgmentSignature);
    }
}
