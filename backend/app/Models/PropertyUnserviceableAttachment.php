<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyUnserviceableAttachment extends Model
{
    use HasFactory;

    protected $table = 'property_unserviceable_attachments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'unserviceable_report_id',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(PropertyUnserviceableReport::class, 'unserviceable_report_id', 'id');
    }
}
