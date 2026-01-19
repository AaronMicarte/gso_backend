<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;

class RequisitionIssueSlip extends Model
{
    use SoftDeletes;

    protected $table = 'requisition_issue_slips';

    protected $fillable = [
        'asset_request_id',
        'ris_number',
        'entity_name',
        'fund_cluster',
        'division',
        'responsibility_center_code',
        'purpose',
        'status',
        'requested_by_user_id',
        'requested_at',
        'approved_by_user_id',
        'approved_at',
        'prepared_by_user_id',
        'prepared_at',
        'issued_by_user_id',
        'issued_at',
        'received_by_user_id',
        'received_at',
        'remarks',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'prepared_at' => 'datetime',
        'issued_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function assetRequest(): BelongsTo
    {
        return $this->belongsTo(AssetRequest::class, 'asset_request_id', 'RequestID');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RequisitionIssueSlipItem::class, 'ris_id', 'id');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(RequisitionIssueSlipSignature::class, 'ris_id', 'id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id', 'UserID');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'UserID');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id', 'UserID');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id', 'UserID');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id', 'UserID');
    }
}
