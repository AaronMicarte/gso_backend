<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreClassificationRequest;
use App\Http\Requests\MasterData\UpdateClassificationRequest;
use App\Http\Resources\MasterData\ClassificationResource;
use App\Services\MasterData\ClassificationService;

class ClassificationController extends BaseMasterDataController
{
    public function __construct(ClassificationService $service)
    {
        $this->service = $service;
        $this->resourceClass = ClassificationResource::class;
        $this->entityName = 'classification';
    }
}
