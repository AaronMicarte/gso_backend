<?php

namespace App\Services\MasterData;

use App\Models\MasterData\PropertyType;

class PropertyTypeService extends BaseMasterDataService
{
    protected $searchableFields = ['PropertyTypeName', 'Description'];
    protected $defaultSortField = 'PropertyTypeID';

    public function __construct()
    {
        $this->model = PropertyType::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (!empty($params['classification_id'])) {
            $query->where('ClassificationID', $params['classification_id']);
        }

        if (!empty($params['property_classification_id'])) {
            $query->where('PropertyClassificationID', $params['property_classification_id']);
        }
    }
}
