<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ClassificationID,
            'category_id' => $this->CategoryID,
            'name' => $this->ClassificationName,
            'description' => $this->Description,
            'created_at' => $this->CreatedDate?->format('Y-m-d H:i:s'),
            'updated_at' => $this->UpdatedDate?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
            'category' => $this->whenLoaded('category'),
        ];
    }
}
