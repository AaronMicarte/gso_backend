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

    public function getAll(array $params): array
    {
        $query = $this->model::query()
            ->with([
                'propertyType.propertyClassification',
                'defaultUnitOfMeasure',
            ]);

        if (isset($params['include_deleted']) && $params['include_deleted']) {
            $query->withTrashed();
        }

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                foreach ($this->searchableFields as $field) {
                    $q->orWhere($field, 'like', "%{$params['search']}%");
                }
            });
        }

        $this->applyFilters($query, $params);

        $modelInstance = new $this->model;
        $sortBy = $params['sort_by'] ?? ($this->defaultSortField ?: $modelInstance->getKeyName());
        if (in_array(strtolower($sortBy), ['id'])) {
            $sortBy = $modelInstance->getKeyName();
        }
        $allowedSortFields = array_merge($this->searchableFields, $modelInstance->getFillable(), [$modelInstance->getKeyName(), 'CreatedDate', 'UpdatedDate']);
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = $this->defaultSortField ?: $modelInstance->getKeyName();
        }
        $sortOrder = $params['sort_order'] ?? $this->defaultSortOrder;
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $params['per_page'] ?? 15;
        if ($perPage === 'all') {
            return [
                'data' => $query->get(),
                'pagination' => null,
            ];
        }

        $paginated = $query->paginate($perPage);

        return [
            'data' => $paginated->items(),
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ],
        ];
    }

    public function findById($id, bool $includeTrashed = false): ?\Illuminate\Database\Eloquent\Model
    {
        $query = $this->model::query()
            ->with([
                'propertyType.propertyClassification',
                'defaultUnitOfMeasure',
            ]);

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    protected function applyFilters($query, array $params): void
    {
        if (array_key_exists('is_active', $params) && $params['is_active'] !== '' && $params['is_active'] !== null) {
            $query->where('IsActive', $params['is_active']);
        }
    }

    /**
     * Get active articles for dropdown/selection
     */
    public function getActiveArticles()
    {
        return $this->model::active()
            ->with([
                'propertyType.propertyClassification',
                'defaultUnitOfMeasure',
            ])
            ->orderBy('ArticleName')
            ->get();
    }
}