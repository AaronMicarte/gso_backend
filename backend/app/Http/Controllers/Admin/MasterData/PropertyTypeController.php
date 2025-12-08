<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StorePropertyTypeRequest;
use App\Http\Requests\MasterData\UpdatePropertyTypeRequest;
use App\Http\Resources\MasterData\PropertyTypeResource;
use App\Services\MasterData\PropertyTypeService;

class PropertyTypeController extends BaseMasterDataController
{
    public function __construct(PropertyTypeService $service)
    {
        $this->service = $service;
        $this->resourceClass = PropertyTypeResource::class;
        $this->entityName = 'property type';
    }
}
