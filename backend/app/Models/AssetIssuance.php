<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\Inventory\PropertyInventoryDetail;

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
     * Generate document number based on type (delegates to PropertyIssuance for consistent YY-##### format)
     */
    public static function generateDocumentNumber(string $type): string
    {
        return \App\Models\PropertyIssuance::generateDocumentNumber($type);
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
