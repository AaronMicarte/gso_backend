<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Auth\User;
use App\Models\MasterData\Department;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $table = 'purchaserequest';
    protected $primaryKey = 'PurchaseRequestID';
    public $timestamps = false; // Using custom CreatedDate/UpdatedDate

    protected $fillable = [
        'PRNumber',
        'RequestedBy',
        'DepartmentID',
        'ApprovedBy',
        'PRStatusID',
        'DateRequested',
        'DateApproved',
        'Purpose',
        'Remarks',
        'CreatedDate',
        'UpdatedDate'
    ];

    protected $casts = [
        'DateRequested' => 'date',
        'DateApproved' => 'date',
        'CreatedDate' => 'datetime',
        'UpdatedDate' => 'datetime',
    ];

    // Relationships
    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class, 'PurchaseRequestID', 'PurchaseRequestID');
    }

    public function status()
    {
        return $this->belongsTo(PurchaseRequestStatus::class, 'PRStatusID', 'PRStatusID');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'RequestedBy', 'UserID');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'ApprovedBy', 'UserID');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'DepartmentID', 'DepartmentID');
    }
}
