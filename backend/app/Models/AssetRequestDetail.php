<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\EndUser;
use App\Models\Auth\Department;

class AssetRequestDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assetrequestdetail';
    protected $primaryKey = 'RequestDetailID';

    protected $fillable = [
        'RequestID',
        'PropertyInventoryDetailID',
        'ItemDescription',
        'Unit',
        'QuantityRequested',
        'Purpose',
        'IntendedEndUser',
        'AdditionalNotes',
        'PropertyClassification',
        'AssignedEndUserID',
        'AssignedDepartmentID',
        'AdminNotes',
        'IsApprovedForIssuance',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'IsApprovedForIssuance' => 'boolean',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssetRequest::class, 'RequestID', 'RequestID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'PropertyInventoryDetailID', 'InventoryDetailID');
    }

    public function assignedEndUser(): BelongsTo
    {
        return $this->belongsTo(EndUser::class, 'AssignedEndUserID', 'EndUserID');
    }

    public function assignedDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'AssignedDepartmentID', 'DepartmentID');
    }

    /**
     * Get the asset assignments for this detail.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'AssetRequestDetailID', 'RequestDetailID');
    }
}
