<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\Auth\Department;
use App\Models\PropertyIssuance;

class AssetRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assetrequest';
    protected $primaryKey = 'RequestID';

    protected $fillable = [
        'RequestNumber',
        'RequestedBy',
        'DepartmentID',
        'Purpose',
        'Status',
        'ReviewedBy',
        'ReviewedAt',
        'ReviewNotes',
        'IssuedBy',
        'IssuedAt',
        'RequestDate',
    ];

    protected $casts = [
        'RequestDate' => 'datetime',
        'ReviewedAt' => 'datetime',
        'IssuedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(AssetRequestDetail::class, 'RequestID', 'RequestID');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'RequestedBy', 'UserID');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'DepartmentID', 'DepartmentID');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ReviewedBy', 'UserID');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IssuedBy', 'UserID');
    }

    public function propertyIssuance(): HasMany
    {
        return $this->hasMany(PropertyIssuance::class, 'RequestID', 'RequestID');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AssetAuditLog::class, 'EntityID', 'RequestID')
            ->where('EntityType', 'asset_request');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(AssetRequestSignature::class, 'request_id', 'RequestID');
    }

    public function ris()
    {
        return $this->hasOne(\App\Models\RequisitionIssueSlip::class, 'asset_request_id', 'RequestID');
    }

    /**
     * Generate next request number
     */
    public static function generateRequestNumber(): string
    {
        $year = date('Y');
        $prefix = "REQ-{$year}-";
        
        $lastRequest = self::where('RequestNumber', 'like', $prefix . '%')
            ->orderBy('RequestID', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->RequestNumber, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if request can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->Status === 'pending';
    }

    /**
     * Check if request can be reviewed
     */
    public function canBeReviewed(): bool
    {
        return $this->Status === 'pending';
    }

    /**
     * Check if request can be issued
     */
    public function canBeIssued(): bool
    {
        return $this->Status === 'approved';
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->Status, ['pending', 'approved']);
    }
}
