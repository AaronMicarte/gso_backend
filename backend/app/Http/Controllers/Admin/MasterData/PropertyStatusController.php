<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StorePropertyStatusRequest;
use App\Http\Requests\MasterData\UpdatePropertyStatusRequest;
use App\Http\Resources\MasterData\PropertyStatusResource;
use App\Services\MasterData\PropertyStatusService;

class PropertyStatusController extends BaseMasterDataController
{
    public function __construct(PropertyStatusService $service)
    {
        $this->service = $service;
        $this->resourceClass = PropertyStatusResource::class;
        $this->entityName = 'property status';
    }
}
