<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'user';
    protected $primaryKey = 'UserID';

    /**
     * Disable Laravel's automatic timestamps (we use CreatedDate/UpdatedDate)
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'UserRoleID',
        'DepartmentID',
        'Username',
        'Email',
        'Password',
        'FullName',
        'IsActive',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
        ];
    }

    /**
     * Get the user role that owns the user.
     */
    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'UserRoleID', 'UserRoleID');
    }

    /**
     * Get the department that owns the user.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'DepartmentID', 'DepartmentID');
    }

    /**
     * Get the login audits for the user.
     */
    public function loginAudits(): HasMany
    {
        return $this->hasMany(LoginAudit::class, 'UserID', 'UserID');
    }

    /**
     * Get the name of the unique identifier for the user.
     * This is the primary key column used by Laravel's auth system.
     */
    public function getAuthIdentifierName(): string
    {
        return 'UserID';
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->Password;
    }
}
