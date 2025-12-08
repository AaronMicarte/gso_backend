<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IIRUPRecommendation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'iiruprecommendation';
    protected $primaryKey = 'RecommendationID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'RecommendationName',
        'Description',
    ];
}
