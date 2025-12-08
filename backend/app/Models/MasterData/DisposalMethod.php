<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisposalMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'disposalmethod';
    protected $primaryKey = 'DisposalMethodID';
    public $timestamps = false;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'MethodName',
        'Description',
    ];
}
