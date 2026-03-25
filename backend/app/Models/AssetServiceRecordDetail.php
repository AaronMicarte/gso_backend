<?php

namespace App\Models;

use App\Models\Auth\User;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Supplier;
use App\Models\MasterData\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetServiceRecordDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_service_records_details';
    protected $primaryKey = 'ServiceRecordDetailID';

    protected $fillable = [
        'ServiceRecordID',
        'InventoryDetailID',
        'PropertyNumber',
        'ItemDescription',
        'ProblemDescription',
        'HandlingDecision',
        'BudgetStatus',
        'Status',
        'AssignedVendorID',
        'AssignedSupplierID',
        'EstimatedCost',
        'ActualCost',
        'WarrantyClaimNumber',
        'RepairOrderNumber',
        'InvoiceAttachment',
        'SupportingAttachments',
        'CreatedByUserID',
        'UpdatedByUserID',
    ];

    protected $casts = [
        'EstimatedCost' => 'decimal:2',
        'ActualCost' => 'decimal:2',
        'SupportingAttachments' => 'array',
    ];

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(AssetServiceRecord::class, 'ServiceRecordID', 'ServiceRecordID');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(PropertyInventoryDetail::class, 'InventoryDetailID', 'InventoryDetailID');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'AssignedVendorID', 'VendorID');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'AssignedSupplierID', 'SupplierID');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CreatedByUserID', 'UserID');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UpdatedByUserID', 'UserID');
    }
}
