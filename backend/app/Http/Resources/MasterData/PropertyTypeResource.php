<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $classification = $this->propertyClassification;
        $documentType = $classification?->DocumentType;

        return [
            'id' => $this->PropertyTypeID,
            'property_classification_id' => $this->PropertyClassificationID,
            'property_classification_label' => $documentType === 'ICS' ? 'Semi Expe' : ($documentType === 'PAR' ? 'PPE' : null),
            'name' => $this->PropertyName,
            'description' => $this->Description,
            'created_at' => $this->CreatedDate?->format('Y-m-d H:i:s'),
            'updated_at' => $this->UpdatedDate?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
            'property_classification' => $classification ? [
                'id' => $classification->PropertyClassificationID,
                'name' => $classification->ClassificationName,
                'document_type' => $classification->DocumentType,
            ] : null,
        ];
    }
}
