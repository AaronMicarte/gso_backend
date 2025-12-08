<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Position;

class PositionService extends BaseMasterDataService
{
    protected $searchableFields = ['PositionName', 'Description'];
    protected $defaultSortField = 'PositionID';

    public function __construct()
    {
        $this->model = Position::class;
    }
}
