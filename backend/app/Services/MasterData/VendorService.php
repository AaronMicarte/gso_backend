<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Vendor;

class VendorService extends BaseMasterDataService
{
    protected $searchableFields = ['VendorName', 'ContactPerson', 'Email', 'ContactNumber'];
    protected $defaultSortField = 'VendorID';

    public function __construct()
    {
        $this->model = Vendor::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (isset($params['is_active'])) {
            $query->where('IsActive', $params['is_active']);
        }
    }
}
