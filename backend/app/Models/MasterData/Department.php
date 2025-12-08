<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'department';
    protected $primaryKey = 'DepartmentID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'DepartmentCode',
        'DepartmentName',
        'Description',
        'CreatedDate',
        'UpdatedDate',
    ];

    protected $casts = [
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

    public function users()
    {
        return $this->hasMany(\App\Models\Auth\User::class, 'DepartmentID', 'DepartmentID');
    }

    public function endUsers()
    {
        return $this->hasMany(\App\Models\MasterData\EndUser::class, 'DepartmentID', 'DepartmentID');
    }
}
