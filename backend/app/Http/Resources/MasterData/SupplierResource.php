<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->SupplierID,
            'name' => $this->SupplierName,
            'address' => $this->Address,
            'contact_person' => $this->ContactPerson,
            'contact_number' => $this->ContactNumber,
            'email' => $this->Email,
            'tin' => $this->TIN,
            'is_active' => (bool)$this->IsActive,
            'created_at' => $this->CreatedDate?->format('Y-m-d H:i:s'),
            'updated_at' => $this->UpdatedDate?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
        ];
    }
}
