<?php

namespace App\Services\MasterData;

use App\Models\MasterData\EndUser;

class EndUserService extends BaseMasterDataService
{
    protected $searchableFields = ['FirstName', 'LastName', 'Email', 'ContactNumber'];
    protected $defaultSortField = 'EndUserID';

    public function __construct()
    {
        $this->model = EndUser::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (!empty($params['department_id'])) {
            $query->where('DepartmentID', $params['department_id']);
        }

        if (!empty($params['position_id'])) {
            $query->where('PositionID', $params['position_id']);
        }
    }
}
