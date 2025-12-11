<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseMasterDataController extends Controller
{
    protected $service;
    protected $resourceClass;
    protected $entityName;

    /**
     * Get paginated list with search and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->service->getAll($request->all());

            return response()->json([
                'success' => true,
                'data' => $this->transformCollection($result['data']),
                'pagination' => $result['pagination'] ?? null,
            ], 200);
        } catch (Exception $e) {
            Log::error("Error fetching {$this->entityName}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Failed to fetch {$this->entityName}. Please try again.",
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get single record by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $item = $this->service->findById($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($this->entityName) . ' not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->transformItem($item),
            ], 200);
        } catch (Exception $e) {
            Log::error("Error fetching {$this->entityName} #{$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to fetch {$this->entityName}. Please try again.",
            ], 500);
        }
    }

    /**
     * Create new record
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Get validated data from FormRequest or regular Request
            $data = method_exists($request, 'validated') ? $request->validated() : $request->all();
            $item = $this->service->create($data);

            return response()->json([
                'success' => true,
                'message' => ucfirst($this->entityName) . ' created successfully.',
                'data' => $this->transformItem($item),
            ], 201);
        } catch (Exception $e) {
            Log::error("Error creating {$this->entityName}: " . $e->getMessage(), [
                'data' => isset($data) ? $data : [],
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Failed to create {$this->entityName}. Please try again.",
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update existing record
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Get validated data from FormRequest or regular Request
            $data = method_exists($request, 'validated') ? $request->validated() : $request->all();
            $item = $this->service->update($id, $data);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($this->entityName) . ' not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($this->entityName) . ' updated successfully.',
                'data' => $this->transformItem($item),
            ], 200);
        } catch (Exception $e) {
            Log::error("Error updating {$this->entityName} #{$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Failed to update {$this->entityName}. Please try again.",
            ], 500);
        }
    }

    /**
     * Soft delete record
     */
    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($this->entityName) . ' not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($this->entityName) . ' deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            Log::error("Error deleting {$this->entityName} #{$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to delete {$this->entityName}. Please try again.",
            ], 500);
        }
    }

    /**
     * Restore soft deleted record
     */
    public function restore($id): JsonResponse
    {
        try {
            $item = $this->service->restore($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($this->entityName) . ' not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($this->entityName) . ' restored successfully.',
                'data' => $this->transformItem($item),
            ], 200);
        } catch (Exception $e) {
            Log::error("Error restoring {$this->entityName} #{$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to restore {$this->entityName}. Please try again.",
            ], 500);
        }
    }

    /**
     * Transform single item using resource class
     */
    protected function transformItem($item)
    {
        return $this->resourceClass ? new $this->resourceClass($item) : $item;
    }

    /**
     * Transform collection using resource class
     */
    protected function transformCollection($items)
    {
        if (!$this->resourceClass) {
            return $items;
        }

        return $this->resourceClass::collection($items);
    }
}
