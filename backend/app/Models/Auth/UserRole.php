<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRole extends Model
{
    protected $table = 'userrole';
    protected $primaryKey = 'UserRoleID';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Role',
    ];

    /**
     * Get the users for the user role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'UserRoleID', 'UserRoleID');
    }
}
