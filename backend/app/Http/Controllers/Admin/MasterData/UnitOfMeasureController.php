<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreUnitOfMeasureRequest;
use App\Http\Requests\MasterData\UpdateUnitOfMeasureRequest;
use App\Http\Resources\MasterData\UnitOfMeasureResource;
use App\Services\MasterData\UnitOfMeasureService;

class UnitOfMeasureController extends BaseMasterDataController
{
    public function __construct(UnitOfMeasureService $service)
    {
        $this->service = $service;
        $this->resourceClass = UnitOfMeasureResource::class;
        $this->entityName = 'unit of measure';
    }
}
