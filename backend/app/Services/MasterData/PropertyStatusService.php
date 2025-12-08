<?php

namespace App\Services\MasterData;

use App\Models\MasterData\PropertyStatus;

class PropertyStatusService extends BaseMasterDataService
{
    protected $searchableFields = ['StatusName', 'Description'];
    protected $defaultSortField = 'PropertyStatusID';

    public function __construct()
    {
        $this->model = PropertyStatus::class;
    }
}
