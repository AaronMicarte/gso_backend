<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $propertyType = $this->propertyType;
        $documentType = $propertyType?->propertyClassification?->DocumentType;

        return [
            'id' => $this->ArticleID,
            'name' => $this->ArticleName,
            'description' => $this->Description,
            'image_path' => $this->ArticleImage,
            'image_url' => $this->ArticleImage ? Storage::disk('public')->url($this->ArticleImage) : null,
            'property_type_id' => $this->PropertyTypeID,
            'property_type_name' => $propertyType?->PropertyName,
            'property_classification_label' => $documentType === 'ICS' ? 'Semi Expe' : ($documentType === 'PAR' ? 'PPE' : null),
            'default_unit_of_measure_id' => $this->DefaultUnitOfMeasureID,
            'is_active' => $this->IsActive,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
            'property_type' => $this->whenLoaded('propertyType'),
            'default_unit_of_measure' => $this->whenLoaded('defaultUnitOfMeasure'),
        ];
    }
}