<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EndUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->EndUserID,
            'full_name' => $this->FullName,
            'department_id' => $this->DepartmentID,
            'position_id' => $this->PositionID,
            'contact_number' => $this->ContactNumber,
            'email' => $this->Email,
            'is_active' => (bool)$this->IsActive,
            'created_at' => $this->CreatedDate?->format('Y-m-d H:i:s'),
            'updated_at' => $this->UpdatedDate?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
            'department' => $this->whenLoaded('department'),
            'position' => $this->whenLoaded('position'),
        ];
    }
}
