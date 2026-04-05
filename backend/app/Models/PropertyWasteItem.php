<?php

namespace App\Models;

use App\Models\Auth\User;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Condition;
use App\Models\MasterData\DisposalMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyWasteItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'property_waste_items';
    protected $primaryKey = 'PropertyWasteItemID';

    protected $fillable = [
        'PropertyWasteReportID',
        'ServiceRecordDetailID',
        'InventoryDetailID',
        'PropertyNumber',
        'ItemDescription',
        'ConditionID',
        'DisposalMethodID',
        'DisposalMethodNote',
        'Status',
        'ActionedByUserID',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(PropertyWasteReport::class, 'PropertyWasteReportID', 'PropertyWasteReportID');
    }

    public function serviceRecordDetail(): BelongsTo
    {
        return $this->belongsTo(AssetServiceRecordDetail::class, 'ServiceRecordDetailID', 'ServiceRecordDetailID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID')->withTrashed();
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'ConditionID', 'ConditionID');
    }

    public function disposalMethod(): BelongsTo
    {
        return $this->belongsTo(DisposalMethod::class, 'DisposalMethodID', 'DisposalMethodID');
    }

    public function actionedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ActionedByUserID', 'UserID');
    }
}
