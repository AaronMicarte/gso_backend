<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Category;

class CategoryService extends BaseMasterDataService
{
    protected $searchableFields = ['CategoryName', 'Description'];
    protected $defaultSortField = 'CategoryID';

    public function __construct()
    {
        $this->model = Category::class;
    }

    /**
     * Apply custom filters
     */
    protected function applyFilters($query, array $params): void
    {
        // Add any category-specific filters here
        // Example: Filter by date range
        if (!empty($params['created_from'])) {
            $query->whereDate('CreatedDate', '>=', $params['created_from']);
        }

        if (!empty($params['created_to'])) {
            $query->whereDate('CreatedDate', '<=', $params['created_to']);
        }
    }
}
