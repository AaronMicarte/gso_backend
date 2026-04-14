<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RsmiReport extends Model
{
    use SoftDeletes;

    protected $table = 'rsmi_header';

    protected $fillable = [
        'report_date',
        'serial_no',
        'serial_year',
        'serial_month',
        'serial_sequence',
        'entity_name',
        'fund_cluster',
        'created_by_user_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RsmiReportItem::class, 'rsmi_header_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'UserID');
    }
}
