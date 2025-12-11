<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyClassificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PropertyClassificationID,
            'name' => $this->ClassificationName,
            'description' => $this->Description,
            'document_type' => $this->DocumentType,
            'created_at' => null,
            'updated_at' => null,
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
        ];
    }
}
