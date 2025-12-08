<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ConditionID,
            'name' => $this->ConditionName,
            'description' => $this->Description,
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
        ];
    }
}
