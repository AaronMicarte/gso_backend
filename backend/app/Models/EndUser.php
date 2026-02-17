<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\Department;

class EndUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'enduser';
    protected $primaryKey = 'EndUserID';

    protected $fillable = [
        'PositionID',
        'DepartmentID', 
        'FullName',
        'ContactNumber',
        'Email',
        'IsActive',
    ];

    protected $appends = [
        'DepartmentName',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'CreatedDate' => 'datetime',
        'UpdatedDate' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'DepartmentID', 'DepartmentID');
    }

    public function getDepartmentNameAttribute(): ?string
    {
        return $this->department?->DepartmentName;
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'PositionID', 'PositionID');
    }
}