<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class AssetRequestSignature extends Model
{
    protected $table = 'asset_request_signatures';

    protected $fillable = [
        'request_id',
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
        'user_agent',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size_bytes' => 'integer',
    ];

    public function assetRequest(): BelongsTo
    {
        return $this->belongsTo(AssetRequest::class, 'request_id', 'RequestID');
    }

    public function signedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id', 'UserID');
    }
}
