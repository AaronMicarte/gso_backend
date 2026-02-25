<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Department;

class DepartmentService extends BaseMasterDataService
{
    protected $searchableFields = ['DepartmentCode', 'DepartmentName', 'Description'];
    // default to alphabetical by name
    protected $defaultSortField = 'DepartmentName';
    protected $defaultSortOrder = 'asc';

    public function __construct()
    {
        $this->model = Department::class;
    }
}
