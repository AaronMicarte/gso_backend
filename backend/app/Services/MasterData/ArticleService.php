<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Article;

class ArticleService extends BaseMasterDataService
{
    protected $searchableFields = ['ArticleName', 'Description'];
    protected $defaultSortField = 'ArticleID';

    public function __construct()
    {
        $this->model = Article::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (!empty($params['is_active'])) {
            $query->where('IsActive', $params['is_active']);
        }
    }

    /**
     * Get active articles for dropdown/selection
     */
    public function getActiveArticles()
    {
        return $this->model::active()->orderBy('ArticleName')->get();
    }
}