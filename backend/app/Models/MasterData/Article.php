<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articles';
    protected $primaryKey = 'ArticleID';
    public $timestamps = true;
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'ArticleName',
        'Description',
        'PropertyTypeID',
        'DefaultUnitOfMeasureID',
        'IsActive',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function propertyType()
    {
        return $this->belongsTo(\App\Models\MasterData\PropertyType::class, 'PropertyTypeID', 'PropertyTypeID');
    }

    public function defaultUnitOfMeasure()
    {
        return $this->belongsTo(\App\Models\MasterData\UnitOfMeasure::class, 'DefaultUnitOfMeasureID', 'UnitOfMeasureID');
    }

    /**
     * Scope for active articles only
     */
    public function scopeActive($query)
    {
        return $query->where('IsActive', true);
    }

    /**
     * Scope for searching by name
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where('ArticleName', 'like', '%' . $search . '%');
    }

    /**
     * Get delivery receipt details that use this article
     */
    public function deliveryReceiptDetails()
    {
        return $this->hasMany(\App\Models\Procurement\DeliveryReceiptDetail::class, 'ArticleID', 'ArticleID');
    }
}