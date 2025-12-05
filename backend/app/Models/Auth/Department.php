<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'department';
    protected $primaryKey = 'DepartmentID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'DepartmentName',
        'DepartmentCode',
        'IsActive',
    ];

    /**
     * Get the users for the department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'DepartmentID', 'DepartmentID');
    }
}
