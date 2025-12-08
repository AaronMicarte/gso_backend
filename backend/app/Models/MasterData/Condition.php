<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'condition';
    protected $primaryKey = 'ConditionID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'ConditionName',
        'Description',
    ];
}
