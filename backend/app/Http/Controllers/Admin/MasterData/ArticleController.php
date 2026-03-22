<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Requests\MasterData\StoreArticleRequest;
use App\Http\Requests\MasterData\UpdateArticleRequest;
use App\Http\Resources\MasterData\ArticleResource;
use App\Services\MasterData\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate((new StoreArticleRequest())->rules());

            if ($request->hasFile('ArticleImage')) {
                $data['ArticleImage'] = $request->file('ArticleImage')->store('article_images', 'public');
            }

            $item = $this->service->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Article created successfully.',
                'data' => $this->transformItem($item),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating article: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create article. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $existing = $this->service->findById($id);
            if (!$existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found.',
                ], 404);
            }

            $oldImagePath = $existing->ArticleImage;

            $updateRequest = new UpdateArticleRequest();
            $updateRequest->setRouteResolver($request->getRouteResolver());
            $data = $request->validate($updateRequest->rules());

            $removeImage = !empty($data['RemoveImage']);
            unset($data['RemoveImage']);

            if ($request->hasFile('ArticleImage')) {
                $data['ArticleImage'] = $request->file('ArticleImage')->store('article_images', 'public');
            } elseif ($removeImage) {
                $data['ArticleImage'] = null;
            }

            $item = $this->service->update($id, $data);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found.',
                ], 404);
            }

            $newImagePath = $item->ArticleImage;
            if ($oldImagePath && $oldImagePath !== $newImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Article updated successfully.',
                'data' => $this->transformItem($item),
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating article #{$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update article. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}