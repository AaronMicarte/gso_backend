<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'position';
    protected $primaryKey = 'PositionID';

    protected $fillable = [
        'PositionName',
        'PositionDescription',
    ];

    protected $casts = [
        'CreatedDate' => 'datetime',
        'UpdatedDate' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}