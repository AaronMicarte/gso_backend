<?php

namespace App\Http\Resources\Qr;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\QrCode */
class QrCodeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'QRCodeID' => (int) $this->QRCodeID,
            'PropertyID' => (int) $this->PropertyID,
            'InventoryDetailID' => $this->InventoryDetailID !== null ? (int) $this->InventoryDetailID : null,
            'DepartmentID' => (int) $this->DepartmentID,
            'QRCodeData' => $this->QRCodeData,
            'DateGenerated' => $this->DateGenerated,
        ];
    }
}
