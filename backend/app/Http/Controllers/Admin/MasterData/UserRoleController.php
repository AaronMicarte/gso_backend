<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreUserRoleRequest;
use App\Http\Requests\MasterData\UpdateUserRoleRequest;
use App\Http\Resources\MasterData\UserRoleResource;
use App\Services\MasterData\UserRoleService;

class UserRoleController extends BaseMasterDataController
{
    public function __construct(UserRoleService $service)
    {
        $this->service = $service;
        $this->resourceClass = UserRoleResource::class;
        $this->entityName = 'user role';
    }
}
