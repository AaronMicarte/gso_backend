<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAudit extends Model
{
    protected $table = 'loginaudit';
    protected $primaryKey = 'LoginAuditID';
    public $timestamps = false; // Disable automatic timestamps

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'UserID',
        'EventTime',
        'Status',
    ];

    /**
     * Get the user that owns the login audit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
