<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Notification\UserPushToken;
use App\Models\Notification\UserNotification;

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
        'MFAEnabled',
        'MFASecret',
        'BackupCodes',
        'EmailVerifiedAt',
        'ActivationTokenHash',
        'ActivationSentAt',
        'ActivationExpiresAt',
        'MustChangePassword',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'Password',
        'remember_token',
        'MFASecret',
        'BackupCodes',
        'ActivationTokenHash',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'FirstName',
        'LastName',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
            'IsActive' => 'boolean',
            'MFAEnabled' => 'boolean',
            'MustChangePassword' => 'boolean',
            'EmailVerifiedAt' => 'datetime',
            'ActivationSentAt' => 'datetime',
            'ActivationExpiresAt' => 'datetime',
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
     * Get the trusted devices for the user.
     */
    public function trustedDevices(): HasMany
    {
        return $this->hasMany(TrustedDevice::class, 'UserID', 'UserID');
    }

    /**
     * Get push tokens registered by this user.
     */
    public function pushTokens(): HasMany
    {
        return $this->hasMany(UserPushToken::class, 'UserID', 'UserID');
    }

    /**
     * Get notifications addressed to this user.
     */
    public function appNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'UserID', 'UserID');
    }

    /**
     * Get the first name (accessor for frontend compatibility).
     */
    public function getFirstNameAttribute(): string
    {
        $fullName = $this->FullName ?? '';
        $parts = preg_split('/\s+/', trim($fullName), 2);
        return $parts[0] ?? '';
    }

    /**
     * Get the last name (accessor for frontend compatibility).
     */
    public function getLastNameAttribute(): string
    {
        $fullName = $this->FullName ?? '';
        $parts = preg_split('/\s+/', trim($fullName), 2);
        return $parts[1] ?? '';
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

    /**
     * Get the role ID (alias for UserRoleID for backward compatibility).
     */
    public function getRoleIDAttribute(): ?int
    {
        return $this->UserRoleID;
    }
}
