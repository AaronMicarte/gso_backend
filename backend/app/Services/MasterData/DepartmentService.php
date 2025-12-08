<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Department;

class DepartmentService extends BaseMasterDataService
{
    protected $searchableFields = ['DepartmentCode', 'DepartmentName', 'Description'];
    protected $defaultSortField = 'DepartmentID';

    public function __construct()
    {
        $this->model = Department::class;
    }
}
