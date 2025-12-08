<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Supplier;

class SupplierService extends BaseMasterDataService
{
    protected $searchableFields = ['SupplierName', 'ContactPerson', 'Email', 'ContactNumber', 'TIN'];
    protected $defaultSortField = 'SupplierID';

    public function __construct()
    {
        $this->model = Supplier::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (isset($params['is_active'])) {
            $query->where('IsActive', $params['is_active']);
        }
    }
}
