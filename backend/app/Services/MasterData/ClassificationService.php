<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Classification;

class ClassificationService extends BaseMasterDataService
{
    protected $searchableFields = ['ClassificationName', 'Description'];
    protected $defaultSortField = 'ClassificationID';

    public function __construct()
    {
        $this->model = Classification::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (!empty($params['category_id'])) {
            $query->where('CategoryID', $params['category_id']);
        }
    }
}
