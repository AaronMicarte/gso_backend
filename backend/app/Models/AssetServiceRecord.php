<?php

namespace App\Models;

use App\Models\Auth\User;
use App\Models\MasterData\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetServiceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_service_records';
    protected $primaryKey = 'ServiceRecordID';

    protected $fillable = [
        'ServiceNumber',
        'DateReported',
        'ReportSource',
        'Status',
        'RecordNotes',
        'CreatedByUserID',
        'UpdatedByUserID',
    ];

    protected $casts = [
        'DateReported' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(AssetServiceRecordDetail::class, 'ServiceRecordID', 'ServiceRecordID');
    }

    public function reportingDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'ReportSource', 'DepartmentID');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CreatedByUserID', 'UserID');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UpdatedByUserID', 'UserID');
    }

    public static function generateServiceNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'SVR-' . $year . '-';

        $latest = self::where('ServiceNumber', 'like', $prefix . '%')
            ->orderByDesc('ServiceRecordID')
            ->value('ServiceNumber');

        $next = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $sequence = (int) end($parts);
            $next = $sequence + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
