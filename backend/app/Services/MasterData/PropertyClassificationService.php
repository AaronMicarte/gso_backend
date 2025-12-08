<?php

namespace App\Services\MasterData;

use App\Models\MasterData\PropertyClassification;

class PropertyClassificationService extends BaseMasterDataService
{
    protected $searchableFields = ['ClassificationName', 'Description', 'DocumentType'];
    protected $defaultSortField = 'PropertyClassificationID';

    public function __construct()
    {
        $this->model = PropertyClassification::class;
    }
}
