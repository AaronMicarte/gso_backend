<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StorePositionRequest;
use App\Http\Requests\MasterData\UpdatePositionRequest;
use App\Http\Resources\MasterData\PositionResource;
use App\Services\MasterData\PositionService;

class PositionController extends BaseMasterDataController
{
    public function __construct(PositionService $service)
    {
        $this->service = $service;
        $this->resourceClass = PositionResource::class;
        $this->entityName = 'position';
    }
}
