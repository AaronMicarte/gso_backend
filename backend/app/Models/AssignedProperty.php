<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedProperty extends Model
{
    protected $table = 'assignedproperty';
    protected $primaryKey = 'PropertyID';

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    protected $fillable = [
        'InventoryDetailID',
        'EndUserID',
        'DepartmentID',
        'PropertyCardNumber',
        'Description',
        'AssignedDate',
        'PropertyStatusID',
        'ConditionID',
        'Remarks',
    ];

    protected $casts = [
        'AssignedDate' => 'date',
        'CreatedDate' => 'datetime',
        'UpdatedDate' => 'datetime',
    ];

    public function inventoryDetail()
    {
        return $this->belongsTo(\App\Models\Inventory\PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }

    public function endUser()
    {
        return $this->belongsTo(\App\Models\EndUser::class, 'EndUserID', 'EndUserID');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\MasterData\Department::class, 'DepartmentID', 'DepartmentID');
    }

    public function condition()
    {
        return $this->belongsTo(\App\Models\MasterData\Condition::class, 'ConditionID', 'ConditionID');
    }

    public function propertyStatus()
    {
        return $this->belongsTo(\App\Models\MasterData\PropertyStatus::class, 'PropertyStatusID', 'PropertyStatusID');
    }

    public function qrCode()
    {
        return $this->hasOne(\App\Models\QrCode::class, 'PropertyID', 'PropertyID');
    }
}
