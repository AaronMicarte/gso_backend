<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustedDevice extends Model
{
    use HasFactory;

    protected $table = 'trusted_devices';
    protected $primaryKey = 'DeviceID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'DeviceToken',
        'DeviceName',
        'UserAgent',
        'IPAddress',
        'CreatedDate',
        'ExpiresAt',
        'LastUsedAt',
        'UpdatedAt',
    ];

    protected function casts(): array
    {
        return [
            'CreatedDate' => 'datetime',
            'ExpiresAt' => 'datetime',
            'LastUsedAt' => 'datetime',
            'UpdatedAt' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this trusted device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Check if the device token is still valid (not expired).
     */
    public function isValid(): bool
    {
        if (!$this->ExpiresAt) {
            return false;
        }

        return now()->lessThanOrEqualTo($this->ExpiresAt);
    }

    /**
     * Update the last used timestamp.
     */
    public function updateLastUsed(): void
    {
        $this->update(['LastUsedAt' => now()]);
    }
}
