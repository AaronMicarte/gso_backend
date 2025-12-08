<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreSupplierRequest;
use App\Http\Requests\MasterData\UpdateSupplierRequest;
use App\Http\Resources\MasterData\SupplierResource;
use App\Services\MasterData\SupplierService;

class SupplierController extends BaseMasterDataController
{
    public function __construct(SupplierService $service)
    {
        $this->service = $service;
        $this->resourceClass = SupplierResource::class;
        $this->entityName = 'supplier';
    }
}
