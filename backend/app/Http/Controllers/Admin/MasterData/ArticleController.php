<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreArticleRequest;
use App\Http\Requests\MasterData\UpdateArticleRequest;
use App\Http\Resources\MasterData\ArticleResource;
use App\Services\MasterData\ArticleService;

class ArticleController extends BaseMasterDataController
{
    public function __construct(ArticleService $service)
    {
        $this->service = $service;
        $this->resourceClass = ArticleResource::class;
        $this->entityName = 'article';
    }

    /**
     * Get active articles for selection dropdowns
     */
    public function active()
    {
        try {
            $articles = $this->service->getActiveArticles();
            
            return response()->json([
                'success' => true,
                'data' => ArticleResource::collection($articles)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active articles. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}