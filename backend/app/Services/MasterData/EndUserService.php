<?php

namespace App\Services\MasterData;

use App\Models\MasterData\EndUser;

class EndUserService extends BaseMasterDataService
{
    protected $searchableFields = ['FullName', 'Email', 'ContactNumber'];
    protected $defaultSortField = 'FullName';
    protected $defaultSortOrder = 'asc';

    public function __construct()
    {
        $this->model = EndUser::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (array_key_exists('is_active', $params) && $params['is_active'] !== null && $params['is_active'] !== '') {
            $isActive = filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isActive !== null) {
                $query->where('IsActive', $isActive);
            }
        }

        if (!empty($params['department_id'])) {
            $query->where('DepartmentID', $params['department_id']);
        }

        if (!empty($params['position_id'])) {
            $query->where('PositionID', $params['position_id']);
        }
    }
}
