<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Auth\User;

class PropertyReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propertyreturn';
    protected $primaryKey = 'ReturnID';

    protected $fillable = [
        'ReturnNumber',
        'BorrowID',
        'ReturnedByUserID',
        'ReturnedAt',
        'Notes',
    ];

    protected $casts = [
        'ReturnedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function borrow(): BelongsTo
    {
        return $this->belongsTo(PropertyBorrow::class, 'BorrowID', 'BorrowID');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ReturnedByUserID', 'UserID');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PropertyReturnItem::class, 'ReturnID', 'ReturnID');
    }

    public static function generateReturnNumber(): string
    {
        $year = date('Y');
        $prefix = 'PBR-RET-' . $year . '-';

        $lastNumber = static::where('ReturnNumber', 'like', $prefix . '%')
            ->orderByDesc('ReturnNumber')
            ->value('ReturnNumber');

        $nextSequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $lastSeq = (int) ($parts[3] ?? 0);
            $nextSequence = $lastSeq + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSequence);
    }
}
