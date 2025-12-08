<?php

namespace App\Services\MasterData;

use App\Models\MasterData\UnitOfMeasure;

class UnitOfMeasureService extends BaseMasterDataService
{
    protected $searchableFields = ['UnitName', 'Description'];
    protected $defaultSortField = 'UnitOfMeasureID';

    public function __construct()
    {
        $this->model = UnitOfMeasure::class;
    }
}
