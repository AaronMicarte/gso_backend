<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitOfMeasure extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unitofmeasure';
    protected $primaryKey = 'UnitOfMeasureID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'UnitName',
        'Description',
    ];
}
