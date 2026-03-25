<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Services\MasterData\VendorService;
use App\Http\Resources\MasterData\VendorResource;

class VendorController extends BaseMasterDataController
{
    public function __construct(VendorService $service)
    {
        $this->service = $service;
        $this->resourceClass = VendorResource::class;
        $this->entityName = 'vendor';
    }
}
