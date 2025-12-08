<?php

namespace App\Services\MasterData;

use App\Models\MasterData\DisposalMethod;

class DisposalMethodService extends BaseMasterDataService
{
    protected $searchableFields = ['MethodName', 'Description'];
    protected $defaultSortField = 'DisposalMethodID';

    public function __construct()
    {
        $this->model = DisposalMethod::class;
    }
}
