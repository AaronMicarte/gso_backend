<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreConditionRequest;
use App\Http\Requests\MasterData\UpdateConditionRequest;
use App\Http\Resources\MasterData\ConditionResource;
use App\Services\MasterData\ConditionService;

class ConditionController extends BaseMasterDataController
{
    public function __construct(ConditionService $service)
    {
        $this->service = $service;
        $this->resourceClass = ConditionResource::class;
        $this->entityName = 'condition';
    }
}
