<?php

namespace App\Services\MasterData;

use App\Models\MasterData\UserRole;

class UserRoleService extends BaseMasterDataService
{
    protected $searchableFields = ['RoleName', 'Description'];
    protected $defaultSortField = 'UserRoleID';

    public function __construct()
    {
        $this->model = UserRole::class;
    }
}
