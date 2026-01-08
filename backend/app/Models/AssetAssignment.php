<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Auth\User;
use App\Models\Auth\Department;

class AssetAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_assignments';
    protected $primaryKey = 'AssignmentID';

    protected $fillable = [
        'AssetRequestDetailID',
        'PropertyInventoryDetailID',
        'AssignedUserID',
        'AssignedDepartmentID',
        'AssignmentType',
        'PropertyClassification',
        'AdminNotes',
        'AssignedBy',
        'AssignedAt',
    ];

    protected $casts = [
        'AssignedAt' => 'datetime',
    ];

    /**
     * Get the asset request detail that owns the assignment.
     */
    public function assetRequestDetail(): BelongsTo
    {
        return $this->belongsTo(AssetRequestDetail::class, 'AssetRequestDetailID', 'RequestDetailID');
    }

    /**
     * Get the property inventory detail that owns the assignment.
     */
    public function propertyInventoryDetail(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\PropertyInventoryDetail::class, 'PropertyInventoryDetailID', 'InventoryDetailID');
    }

    /**
     * Get the assigned user.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'AssignedUserID', 'UserID');
    }

    /**
     * Get the assigned department.
     */
    public function assignedDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'AssignedDepartmentID', 'DepartmentID');
    }

    /**
     * Get the user who made the assignment.
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'AssignedBy', 'UserID');
    }
}
