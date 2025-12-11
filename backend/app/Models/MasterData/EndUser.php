<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EndUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'enduser';
    protected $primaryKey = 'EndUserID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'FullName',
        'DepartmentID',
        'PositionID',
        'ContactNumber',
        'Email',
        'IsActive',
        'CreatedDate',
        'UpdatedDate',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'CreatedDate' => 'datetime',
        'UpdatedDate' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->CreatedDate) {
                $model->CreatedDate = now();
            }
        });

        static::updating(function ($model) {
            $model->UpdatedDate = now();
        });
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\MasterData\Department::class, 'DepartmentID', 'DepartmentID');
    }

    public function position()
    {
        return $this->belongsTo(\App\Models\MasterData\Position::class, 'PositionID', 'PositionID');
    }

    public function getFullNameAttribute()
    {
        return "{$this->FirstName} {$this->MiddleName} {$this->LastName}";
    }
}
