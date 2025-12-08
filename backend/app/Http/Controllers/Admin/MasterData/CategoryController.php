<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreCategoryRequest;
use App\Http\Requests\MasterData\UpdateCategoryRequest;
use App\Http\Resources\MasterData\CategoryResource;
use App\Services\MasterData\CategoryService;

class CategoryController extends BaseMasterDataController
{
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
        $this->resourceClass = CategoryResource::class;
        $this->entityName = 'category';
    }
}
