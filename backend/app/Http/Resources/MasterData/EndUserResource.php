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
            'first_name' => $this->FirstName,
            'last_name' => $this->LastName,
            'middle_name' => $this->MiddleName,
            'full_name' => $this->full_name,
            'department_id' => $this->DepartmentID,
            'position_id' => $this->PositionID,
            'contact_number' => $this->ContactNumber,
            'email' => $this->Email,
            'created_at' => $this->CreatedDate?->format('Y-m-d H:i:s'),
            'updated_at' => $this->UpdatedDate?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
            'department' => $this->whenLoaded('department'),
            'position' => $this->whenLoaded('position'),
        ];
    }
}
