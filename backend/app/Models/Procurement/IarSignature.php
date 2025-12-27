<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class IarSignature extends Model
{
    protected $table = 'iar_signatures';
    protected $primaryKey = 'id';

    protected $fillable = [
        'iar_id',
        'role',
        'signed_by_user_id',
        'source',
        'storage_disk',
        'storage_path',
        'url',
        'mime',
        'width',
        'height',
        'size_bytes',
        'sha256_hash',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size_bytes' => 'integer'
    ];

    public function iar(): BelongsTo
    {
        return $this->belongsTo(InspectionAcceptanceReport::class, 'iar_id', 'IARID');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id', 'UserID');
    }
}
