<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyUnserviceableReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'property_unserviceable_reports';
    protected $primaryKey = 'id';

    protected $fillable = [
        'report_number',
        'status',
        'remarks',
        'created_by_user_id',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PropertyUnserviceableItem::class, 'unserviceable_report_id', 'id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PropertyUnserviceableAttachment::class, 'unserviceable_report_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'UserID');
    }

    public static function generateReportNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'UNS-' . $year . '-';

        $latest = self::withTrashed()
            ->where('report_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('report_number');

        $next = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $sequence = (int) end($parts);
            $next = $sequence + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
