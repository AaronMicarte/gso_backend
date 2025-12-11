<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PropertyTypeID,
            'classification_id' => $this->ClassificationID,
            'property_classification_id' => $this->PropertyClassificationID,
            'name' => $this->PropertyName,
            'description' => $this->Description,
            'created_at' => $this->CreatedDate?->format('Y-m-d H:i:s'),
            'updated_at' => $this->UpdatedDate?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
            'classification' => $this->whenLoaded('classification'),
            'property_classification' => $this->whenLoaded('propertyClassification'),
        ];
    }
}
