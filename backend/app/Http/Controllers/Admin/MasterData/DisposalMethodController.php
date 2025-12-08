<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreDisposalMethodRequest;
use App\Http\Requests\MasterData\UpdateDisposalMethodRequest;
use App\Http\Resources\MasterData\DisposalMethodResource;
use App\Services\MasterData\DisposalMethodService;

class DisposalMethodController extends BaseMasterDataController
{
    public function __construct(DisposalMethodService $service)
    {
        $this->service = $service;
        $this->resourceClass = DisposalMethodResource::class;
        $this->entityName = 'disposal method';
    }
}
