<?php

namespace App\Services\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseMasterDataService
{
    protected $model;
    protected $searchableFields = [];
    protected $defaultSortField = 'id';
    protected $defaultSortOrder = 'desc';

    /**
     * Get all records with pagination, search, and filters
     */
    public function getAll(array $params): array
    {
        $query = $this->model::query();

        // Include soft deleted if requested
        if (isset($params['include_deleted']) && $params['include_deleted']) {
            $query->withTrashed();
        }

        // Search functionality
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                foreach ($this->searchableFields as $field) {
                    $q->orWhere($field, 'like', "%{$params['search']}%");
                }
            });
        }

        // Apply custom filters
        $this->applyFilters($query, $params);

        // Sorting
        $sortBy = $params['sort_by'] ?? $this->defaultSortField;
        $sortOrder = $params['sort_order'] ?? $this->defaultSortOrder;
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
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

    /**
     * Find record by ID
     */
    public function findById($id, bool $includeTrashed = false): ?Model
    {
        $query = $this->model::query();

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->model::create($data);
        });
    }

    /**
     * Update existing record
     */
    public function update($id, array $data): ?Model
    {
        return DB::transaction(function () use ($id, $data) {
            $item = $this->findById($id);

            if (!$item) {
                return null;
            }

            $item->update($data);
            return $item->fresh();
        });
    }

    /**
     * Soft delete record
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $item = $this->findById($id);

            if (!$item) {
                return false;
            }

            return $item->delete();
        });
    }

    /**
     * Restore soft deleted record
     */
    public function restore($id): ?Model
    {
        return DB::transaction(function () use ($id) {
            $item = $this->findById($id, true);

            if (!$item || !$item->trashed()) {
                return null;
            }

            $item->restore();
            return $item->fresh();
        });
    }

    /**
     * Force delete record permanently
     */
    public function forceDelete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $item = $this->findById($id, true);

            if (!$item) {
                return false;
            }

            return $item->forceDelete();
        });
    }

    /**
     * Apply custom filters to query - override in child classes
     */
    protected function applyFilters($query, array $params): void
    {
        // Override in child classes for specific filters
    }
}
