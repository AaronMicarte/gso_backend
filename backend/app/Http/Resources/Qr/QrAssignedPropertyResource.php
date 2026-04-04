<?php

namespace App\Http\Resources\Qr;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\AssignedProperty */
class QrAssignedPropertyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $headName = trim((string) ($this->CurrentDepartmentHead ?? ''));
        $hasHead = $headName !== '';
        $hasDepartment = (int) ($this->DepartmentID ?? 0) > 0;

        return [
            'PropertyID' => (int) $this->PropertyID,
            'InventoryDetailID' => (int) $this->InventoryDetailID,
            'EndUserID' => (int) $this->EndUserID,
            'DepartmentID' => (int) $this->DepartmentID,
            'PropertyCardNumber' => $this->PropertyCardNumber,
            'Description' => $this->Description,
            'AssignedDate' => $this->AssignedDate,
            'PropertyStatusID' => (int) $this->PropertyStatusID,
            'ConditionID' => (int) $this->ConditionID,
            'Remarks' => $this->Remarks,
            'CreatedDate' => $this->CreatedDate,
            'UpdatedDate' => $this->UpdatedDate,
            'inventoryDetail' => $this->whenLoaded('inventoryDetail', function () use ($request) {
                return (new QrInventoryDetailResource($this->inventoryDetail))->toArray($request);
            }),
            'endUser' => $this->whenLoaded('endUser', function () {
                return [
                    'EndUserID' => (int) $this->endUser->EndUserID,
                    'FullName' => $this->endUser->FullName,
                    'DepartmentID' => (int) $this->endUser->DepartmentID,
                    'department' => $this->endUser->relationLoaded('department') && $this->endUser->department
                        ? [
                            'DepartmentID' => (int) $this->endUser->department->DepartmentID,
                            'DepartmentName' => $this->endUser->department->DepartmentName,
                            'DepartmentCode' => $this->endUser->department->DepartmentCode,
                        ]
                        : null,
                ];
            }),
            'department' => $this->whenLoaded('department', function () {
                return [
                    'DepartmentID' => (int) $this->department->DepartmentID,
                    'DepartmentName' => $this->department->DepartmentName,
                    'DepartmentCode' => $this->department->DepartmentCode,
                ];
            }),
            'propertyStatus' => $this->whenLoaded('propertyStatus', function () {
                return [
                    'PropertyStatusID' => (int) $this->propertyStatus->PropertyStatusID,
                    'StatusName' => $this->propertyStatus->StatusName,
                ];
            }),
            'condition' => $this->whenLoaded('condition', function () {
                return [
                    'ConditionID' => (int) $this->condition->ConditionID,
                    'ConditionName' => $this->condition->ConditionName,
                ];
            }),
            'qrCode' => $this->whenLoaded('qrCode', function () use ($request) {
                return $this->qrCode ? (new QrCodeResource($this->qrCode))->toArray($request) : null;
            }),
            // requester: the full name of the user who requested this issuance (if available via issuance -> request)
            'requester' => $this->RequesterFullName ?? null,
            'documentType' => $this->DocumentType ?? null,
            'currentDepartmentHead' => $hasHead ? $headName : null,
            'departmentHeadVacant' => $hasDepartment && !$hasHead,
            'accountabilityLabel' => $hasHead ? $headName : ($hasDepartment ? 'No current department head' : null),
        ];
    }
}
