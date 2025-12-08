<?php

namespace App\Http\Resources\MasterData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisposalMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->DisposalMethodID,
            'name' => $this->MethodName,
            'description' => $this->Description,
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'is_deleted' => $this->trashed(),
        ];
    }
}
