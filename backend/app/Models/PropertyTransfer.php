<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\MasterData\Department;
use App\Models\EndUser;

class PropertyTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertytransfer';
    protected $primaryKey = 'TransferID';

    protected $fillable = [
        'TransferNumber',
        'RequestedByUserID',
        'FromDepartmentID',
        'ToDepartmentID',
        'FromEndUserID',
        'ToEndUserID',
        'Reason',
        'RequestedItemsCount',
        'ReleasingDecision',
        'ReleasingApprovedCount',
        'ReleasingDecisionReason',
        'ReleasingReviewedByUserID',
        'ReleasingReviewedAt',
        'Status',
        'ApprovedByUserID',
        'ApprovedAt',
        'CompletedByUserID',
        'CompletedAt',
        'BorrowerSignedAt',
        'BorrowingSignedAt',
        'GsoNotes',
    ];

    protected $casts = [
        'RequestedItemsCount' => 'integer',
        'ReleasingApprovedCount' => 'integer',
        'ApprovedAt' => 'datetime',
        'ReleasingReviewedAt' => 'datetime',
        'CompletedAt' => 'datetime',
        'BorrowerSignedAt' => 'datetime',
        'BorrowingSignedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PropertyTransferItem::class, 'TransferID', 'TransferID');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'RequestedByUserID', 'UserID');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ApprovedByUserID', 'UserID');
    }

    public function releasingReviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ReleasingReviewedByUserID', 'UserID');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CompletedByUserID', 'UserID');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'FromDepartmentID', 'DepartmentID')->withTrashed();
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'ToDepartmentID', 'DepartmentID')->withTrashed();
    }

    public function fromEndUser(): BelongsTo
    {
        return $this->belongsTo(EndUser::class, 'FromEndUserID', 'EndUserID')->withTrashed();
    }

    public function toEndUser(): BelongsTo
    {
        return $this->belongsTo(EndUser::class, 'ToEndUserID', 'EndUserID')->withTrashed();
    }

    public static function generateTransferNumber(): string
    {
        $year = date('Y');
        $prefix = 'PTR-' . $year . '-';

        $lastNumber = static::where('TransferNumber', 'like', $prefix . '%')
            ->orderByDesc('TransferNumber')
            ->value('TransferNumber');

        $nextSequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $lastSeq = (int) ($parts[2] ?? 0);
            $nextSequence = $lastSeq + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSequence);
    }
}
