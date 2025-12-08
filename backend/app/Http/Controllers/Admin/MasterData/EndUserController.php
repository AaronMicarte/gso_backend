<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreEndUserRequest;
use App\Http\Requests\MasterData\UpdateEndUserRequest;
use App\Http\Resources\MasterData\EndUserResource;
use App\Services\MasterData\EndUserService;

class EndUserController extends BaseMasterDataController
{
    public function __construct(EndUserService $service)
    {
        $this->service = $service;
        $this->resourceClass = EndUserResource::class;
        $this->entityName = 'end user';
    }
}
