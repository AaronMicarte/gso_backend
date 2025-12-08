<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'position';
    protected $primaryKey = 'PositionID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'PositionName',
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

    public function endUsers()
    {
        return $this->hasMany(\App\Models\MasterData\EndUser::class, 'PositionID', 'PositionID');
    }
}
