<?php

namespace App\Models;

use App\Models\Auth\User;
use App\Models\MasterData\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyRlsddpReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'property_rlsddp_reports';
    protected $primaryKey = 'RlsddpID';

    protected $fillable = [
        'RlsddpNumber',
        'DepartmentID',
        'AccountableOfficer',
        'Status',
        'PoliceNotified',
        'PoliceStation',
        'DateReported',
        'Remarks',
        'CreatedByUserID',
        'UpdatedByUserID',
    ];

    protected $casts = [
        'PoliceNotified' => 'boolean',
        'DateReported' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PropertyRlsddpItem::class, 'RlsddpID', 'RlsddpID');
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

    public static function generateRlsddpNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');

        // Serial resets every year and the stored number is formatted as SERIAL-MONTH-YEAR.
        $latest = self::whereYear('created_at', (int) $year)
            ->orderByDesc('RlsddpID')
            ->value('RlsddpNumber');

        $next = 1;
        if ($latest) {
            // Format: ####-MM-YYYY => first segment is the serial.
            $parts    = explode('-', $latest);
            $sequence = (int) ($parts[0] ?? 0);
            $next     = $sequence + 1;
        }

        return str_pad((string) $next, 4, '0', STR_PAD_LEFT) . '-' . $month . '-' . $year;
    }
}
