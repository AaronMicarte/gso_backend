<?php

namespace App\Models;

use App\Models\Auth\User;
use App\Models\MasterData\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyWasteReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'property_waste_reports';
    protected $primaryKey = 'PropertyWasteReportID';

    protected $fillable = [
        'WasteNumber',
        'SourceServiceRecordID',
        'DepartmentID',
        'Status',
        'Notes',
        'CreatedByUserID',
        'UpdatedByUserID',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PropertyWasteItem::class, 'PropertyWasteReportID', 'PropertyWasteReportID');
    }

    public function sourceServiceRecord(): BelongsTo
    {
        return $this->belongsTo(AssetServiceRecord::class, 'SourceServiceRecordID', 'ServiceRecordID');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'DepartmentID', 'DepartmentID');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CreatedByUserID', 'UserID');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UpdatedByUserID', 'UserID');
    }

    public static function generateWasteNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'WST-' . $year . '-';

        $latest = self::where('WasteNumber', 'like', $prefix . '%')
            ->orderByDesc('PropertyWasteReportID')
            ->value('WasteNumber');

        $next = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $sequence = (int) end($parts);
            $next = $sequence + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
