<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertytype';
    protected $primaryKey = 'PropertyTypeID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'ClassificationID',
        'PropertyClassificationID',
        'PropertyTypeName',
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

    public function classification()
    {
        return $this->belongsTo(\App\Models\MasterData\Classification::class, 'ClassificationID', 'ClassificationID');
    }

    public function propertyClassification()
    {
        return $this->belongsTo(\App\Models\MasterData\PropertyClassification::class, 'PropertyClassificationID', 'PropertyClassificationID');
    }
}
