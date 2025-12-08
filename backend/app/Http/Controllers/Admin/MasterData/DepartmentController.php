<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreDepartmentRequest;
use App\Http\Requests\MasterData\UpdateDepartmentRequest;
use App\Http\Resources\MasterData\DepartmentResource;
use App\Services\MasterData\DepartmentService;

class DepartmentController extends BaseMasterDataController
{
    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
        $this->resourceClass = DepartmentResource::class;
        $this->entityName = 'department';
    }
}
