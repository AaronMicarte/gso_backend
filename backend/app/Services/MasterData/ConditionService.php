<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Condition;

class ConditionService extends BaseMasterDataService
{
    protected $searchableFields = ['ConditionName', 'Description'];
    protected $defaultSortField = 'ConditionID';

    public function __construct()
    {
        $this->model = Condition::class;
    }
}
