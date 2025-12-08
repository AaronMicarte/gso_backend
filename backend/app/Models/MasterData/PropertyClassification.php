<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyClassification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyclassification';
    protected $primaryKey = 'PropertyClassificationID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'ClassificationName',
        'Description',
        'DocumentType',
    ];

    public function propertyTypes()
    {
        return $this->hasMany(\App\Models\MasterData\PropertyType::class, 'PropertyClassificationID', 'PropertyClassificationID');
    }
}
