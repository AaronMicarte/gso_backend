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
use App\Models\PropertyReturn;

class PropertyBorrow extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyborrow';
    protected $primaryKey = 'BorrowID';

    protected $fillable = [
        'BorrowNumber',
        'RequestedByUserID',
        'FromDepartmentID',
        'ToDepartmentID',
        'FromEndUserID',
        'ToEndUserID',
        'Purpose',
        'RequestedItemsCount',
        'DueDate',
        'ReleasingDecision',
        'ReleasingApprovedCount',
        'ReleasingDecisionReason',
        'ReleasingReviewedByUserID',
        'ReleasingReviewedAt',
        'Status',
        'ApprovedByUserID',
        'ApprovedAt',
        'BorrowedAt',
        'GsoNotes',
    ];

    protected $casts = [
        'RequestedItemsCount' => 'integer',
        'ReleasingApprovedCount' => 'integer',
        'DueDate' => 'date',
        'ApprovedAt' => 'datetime',
        'ReleasingReviewedAt' => 'datetime',
        'BorrowedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PropertyBorrowItem::class, 'BorrowID', 'BorrowID');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(PropertyReturn::class, 'BorrowID', 'BorrowID');
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

    public static function generateBorrowNumber(): string
    {
        $year = date('Y');
        $prefix = 'PBR-' . $year . '-';

        $lastNumber = static::where('BorrowNumber', 'like', $prefix . '%')
            ->orderByDesc('BorrowNumber')
            ->value('BorrowNumber');

        $nextSequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $lastSeq = (int) ($parts[2] ?? 0);
            $nextSequence = $lastSeq + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSequence);
    }
}
