<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StorePropertyClassificationRequest;
use App\Http\Requests\MasterData\UpdatePropertyClassificationRequest;
use App\Http\Resources\MasterData\PropertyClassificationResource;
use App\Services\MasterData\PropertyClassificationService;

class PropertyClassificationController extends BaseMasterDataController
{
    public function __construct(PropertyClassificationService $service)
    {
        $this->service = $service;
        $this->resourceClass = PropertyClassificationResource::class;
        $this->entityName = 'property classification';
    }
}
