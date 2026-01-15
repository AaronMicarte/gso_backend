<?php

namespace App\Http\Resources\Qr;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Inventory\PropertyInventoryDetail */
class QrInventoryDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'InventoryDetailID' => (int) $this->InventoryDetailID,
            'PropertyNumber' => $this->PropertyNumber,
            'Description' => $this->Description,
            'Brand' => $this->Brand,
            'ModelNumber' => $this->ModelNumber,
            'SerialNumber' => $this->SerialNumber,
            'UnitCost' => $this->UnitCost,
            'PropertyName' => $this->propertyType?->PropertyName ?? $this->PropertyName ?? null,
            'ClassificationName' => $this->propertyType?->propertyClassification?->ClassificationName ?? $this->ClassificationName ?? null,
            'drDetail' => $this->whenLoaded('drDetail', function () {
                return [
                    'DRDetailID' => (int) $this->drDetail->DRDetailID,
                    'ItemDescription' => $this->drDetail->ItemDescription,
                ];
            }),
            'unitOfMeasure' => $this->whenLoaded('unitOfMeasure', function () {
                return [
                    'UnitName' => $this->unitOfMeasure->UnitName,
                ];
            }),
        ];
    }
}
