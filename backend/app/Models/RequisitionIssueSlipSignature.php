<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class RequisitionIssueSlipSignature extends Model
{
    protected $table = 'requisition_issue_slip_signatures';

    protected $fillable = [
        'ris_id',
        'role',
        'signed_by_user_id',
        'source',
        'storage_disk',
        'storage_path',
        'url',
        'mime',
        'width',
        'height',
        'size_bytes',
        'sha256_hash',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size_bytes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ris(): BelongsTo
    {
        return $this->belongsTo(RequisitionIssueSlip::class, 'ris_id', 'id');
    }

    public function signedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id', 'UserID');
    }
}
