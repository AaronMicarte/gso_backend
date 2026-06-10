<?php

namespace App\Services;

use App\Models\AssetAuditLog;
use App\Models\PropertyCardEntry;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\Procurement\IARDetail;
use App\Models\Procurement\InspectionAcceptanceReport;
use Illuminate\Support\Facades\DB;

class PropertyCardLedgerService
{
    public function syncForInventoryDetail(int $inventoryDetailId): void
    {
        $detail = PropertyInventoryDetail::with([
            'inventory.iar',
            'drDetail.deliveryReceipt',
            'drDetail.article',
            'propertyType',
        ])->find($inventoryDetailId);

        if (!$detail) {
            return;
        }

        $this->ensureOpeningEntry($detail);

        $logs = AssetAuditLog::with('performer')
            ->where('EntityType', 'inventory_detail')
            ->where('EntityID', $inventoryDetailId)
            ->orderBy('CreatedAt')
            ->get();

        $unitCost = $detail->UnitCost !== null ? (float) $detail->UnitCost : null;

        foreach ($logs as $log) {
            $mapped = $this->mapAuditLogToEntry($log, $inventoryDetailId);
            if ($mapped === null) {
                continue;
            }

            $qty = max(1, (int) ($mapped['quantity'] ?? 1));
            $receiptQty = $mapped['direction'] === 'receipt' ? $qty : 0;
            $issueQty = $mapped['direction'] === 'issue' ? $qty : 0;

            PropertyCardEntry::updateOrCreate(
                ['SourceKey' => 'audit:' . (int) $log->AuditID],
                [
                    'InventoryDetailID' => $inventoryDetailId,
                    'EntryDate' => $log->CreatedAt ?? now(),
                    'EntryType' => (string) $mapped['entry_type'],
                    'ReceiptQty' => $receiptQty,
                    'IssueQty' => $issueQty,
                    'ReferenceNo' => $mapped['reference'] ?? null,
                    'OfficeOfficer' => $mapped['office_officer'] ?? null,
                    'UnitCost' => $unitCost,
                    'Amount' => $unitCost !== null ? $qty * $unitCost : null,
                    'Remarks' => $mapped['remarks'] ?? null,
                    'SourceType' => 'audit',
                    'SourceID' => (int) $log->AuditID,
                    'CreatedBy' => (int) ($log->PerformedBy ?? 0) ?: null,
                    'CreatedAt' => $log->CreatedAt ?? now(),
                ]
            );
        }
    }

    private function ensureOpeningEntry(PropertyInventoryDetail $detail): void
    {
        $sourceKey = 'opening:' . (int) $detail->InventoryDetailID;
        $referenceNo = $this->resolveOpeningReference($detail);
        $entryDate = $detail->CreatedDate
            ?? $detail->inventory?->DateReceived
            ?? now();
        $qty = max(1, (int) ($detail->Quantity ?? 1));
        $unitCost = $detail->UnitCost !== null ? (float) $detail->UnitCost : null;
        $gsoOfficerName = trim((string) config('gso.gso_officer_name', ''));
        $gsoDepartment = trim((string) config('gso.gso_department_name', 'GSO'));
        $openingOfficeOfficer = trim(implode(' - ', array_filter([$gsoOfficerName, $gsoDepartment])));
        if ($openingOfficeOfficer === '') {
            $openingOfficeOfficer = 'GSO';
        }

        PropertyCardEntry::updateOrCreate(
            ['SourceKey' => $sourceKey],
            [
                'InventoryDetailID' => (int) $detail->InventoryDetailID,
                'EntryDate' => $entryDate,
                'EntryType' => 'acquisition',
                'ReceiptQty' => $qty,
                'IssueQty' => 0,
                'ReferenceNo' => $referenceNo,
                'OfficeOfficer' => $openingOfficeOfficer,
                'UnitCost' => $unitCost,
                'Amount' => $unitCost !== null ? $qty * $unitCost : null,
                'Remarks' => $this->resolveOpeningRemarks($detail),
                'SourceType' => 'opening',
                'SourceID' => (int) $detail->InventoryDetailID,
                'CreatedBy' => null,
                'CreatedAt' => now(),
            ]
        );
    }

    private function resolveOpeningRemarks(PropertyInventoryDetail $detail): string
    {
        $inventoryDetailRemarks = $this->stringOrNull($detail->Remarks ?? null);
        if ($inventoryDetailRemarks !== null) {
            return $inventoryDetailRemarks;
        }

        $deliveryReceiptRemarks = $this->stringOrNull($detail->drDetail?->deliveryReceipt?->Remarks ?? null);
        if ($deliveryReceiptRemarks !== null) {
            return $deliveryReceiptRemarks;
        }

        $inventoryRemarks = $this->stringOrNull($detail->inventory?->Remarks ?? null);
        if ($inventoryRemarks !== null) {
            return $inventoryRemarks;
        }

        return 'Opening entry from PAR/IAR source';
    }

    private function resolveOpeningReference(PropertyInventoryDetail $detail): string
    {
        // User requirement: prioritize PAR reference for the opening row.
        $parNumber = DB::table('propertyissuancedetail as pid')
            ->join('propertyissuance as pi', 'pi.IssuanceID', '=', 'pid.IssuanceID')
            ->where('pid.InventoryDetailID', (int) $detail->InventoryDetailID)
            ->where('pi.DocumentType', 'PAR')
            ->whereNull('pi.deleted_at')
            ->orderBy('pi.DateIssued')
            ->orderBy('pi.IssuanceID')
            ->value('pi.DocumentNumber');

        if (is_string($parNumber) && trim($parNumber) !== '') {
            return trim($parNumber);
        }

        if (!empty($detail->inventory?->iar?->IARNumber)) {
            return (string) $detail->inventory->iar->IARNumber;
        }

        if (!empty($detail->SourceIARDetailID)) {
            $iarDetail = IARDetail::with('iar')->find((int) $detail->SourceIARDetailID);
            if ($iarDetail && $iarDetail->iar && !empty($iarDetail->iar->IARNumber)) {
                return (string) $iarDetail->iar->IARNumber;
            }
        }

        if (!empty($detail->SourceDRDetailID)) {
            $iarDetail = IARDetail::with('iar')
                ->where('DRDetailID', (int) $detail->SourceDRDetailID)
                ->orderByDesc('IARDetailID')
                ->first();
            if ($iarDetail && $iarDetail->iar && !empty($iarDetail->iar->IARNumber)) {
                return (string) $iarDetail->iar->IARNumber;
            }
        }

        $drId = (int) ($detail->drDetail?->DRID ?? 0);
        if ($drId > 0) {
            $iar = InspectionAcceptanceReport::query()
                ->where('DRID', $drId)
                ->orderByDesc('IARID')
                ->first(['IARID', 'IARNumber']);
            if ($iar && !empty($iar->IARNumber)) {
                return (string) $iar->IARNumber;
            }
        }

        if (!empty($detail->drDetail?->deliveryReceipt?->IARNumber)) {
            return (string) $detail->drDetail->deliveryReceipt->IARNumber;
        }

        return (string) ($detail->inventory?->InventoryNumber ?? 'PAR/IAR-N/A');
    }

    private function mapAuditLogToEntry(AssetAuditLog $log, int $inventoryDetailId): ?array
    {
        $action = strtolower((string) $log->Action);
        $details = is_array($log->Details) ? $log->Details : [];

        if ($action === 'issued') {
            $issuanceId = (int) ($details['issuance_id'] ?? 0);

            return [
                'entry_type' => 'issuance',
                'direction' => 'issue',
                'quantity' => 1,
                'reference' => $this->stringOrNull($details['document_number'] ?? null) ?? 'Issuance',
                'office_officer' => $this->stringOrNull($details['issued_to'] ?? null) ?? 'Issued custodian',
                'remarks' => $this->resolveIssuanceRemarks($issuanceId, $inventoryDetailId),
            ];
        }

        if ($action === 'transfer_completed') {
            $transferId = (int) ($details['transfer_id'] ?? 0);

            return [
                'entry_type' => 'transfer',
                'direction' => 'issue',
                'quantity' => 1,
                'reference' => $this->stringOrNull($details['transfer_number'] ?? null) ?? 'Transfer',
                'office_officer' => $this->buildTransferOfficeOfficer($details),
                'remarks' => $this->resolveTransferRemarks($transferId, $inventoryDetailId),
            ];
        }

        if ($action === 'borrow_requested') {
            $borrowId = (int) ($details['borrow_id'] ?? 0);

            return [
                'entry_type' => 'borrow',
                'direction' => 'issue',
                'quantity' => 1,
                'reference' => $this->stringOrNull($details['borrow_number'] ?? null) ?? 'Borrow',
                'office_officer' => 'Borrowing office',
                'remarks' => $this->resolveBorrowRemarks($borrowId, $inventoryDetailId),
            ];
        }

        if ($action === 'borrow_returned') {
            $returnId = (int) ($details['return_id'] ?? 0);

            return [
                'entry_type' => 'borrow_return',
                'direction' => 'receipt',
                'quantity' => (int) ($details['quantity'] ?? 1),
                'reference' => $this->resolveBorrowReturnReference($returnId)
                    ?? $this->stringOrNull($details['borrow_number'] ?? null)
                    ?? 'Borrow Return',
                'office_officer' => 'Returned to accountable office',
                'remarks' => $this->resolveBorrowReturnRemarks($returnId, $inventoryDetailId),
            ];
        }

        if (str_contains($action, 'disposal') || str_contains($action, 'disposed') || str_contains($action, 'waste')) {
            return [
                'entry_type' => 'disposal',
                'direction' => 'issue',
                'quantity' => (int) ($details['quantity'] ?? 1),
                'reference' => $this->stringOrNull($details['waste_number'] ?? null) ?? 'Disposal',
                'office_officer' => 'Disposal committee',
                'remarks' => $this->resolveDisposalRemarks($details),
            ];
        }

        return null;
    }

    private function buildTransferOfficeOfficer(array $details): string
    {
        $toDept = $this->stringOrNull($details['to_department'] ?? null);
        $toUser = $this->stringOrNull($details['to_end_user'] ?? null);

        // If details already contain human-readable labels, prefer them —
        // but detect patterns like "Department #23" or "EndUser #4" and
        // try to resolve them to real names instead of returning raw IDs.
        $resolvedParts = [];
        if ($toDept) {
            if (preg_match('/Department\s*#(\d+)/i', $toDept, $m)) {
                $parsedId = (int) $m[1];
                $resolvedParts[] = $this->resolveDeptLabelFromId($parsedId);
            } else {
                $resolvedParts[] = $toDept;
            }
        }

        if ($toUser) {
            if (preg_match('/EndUser\s*#(\d+)/i', $toUser, $m)) {
                $parsedEndUserId = (int) $m[1];
                // attempt to derive department from enduser
                $endUserDeptId = DB::table('enduser')->where('EndUserID', $parsedEndUserId)->value('DepartmentID');
                if ($endUserDeptId) {
                    $resolvedParts[] = $this->resolveDeptLabelFromId((int) $endUserDeptId);
                } else {
                    // fallback to the enduser full name if available
                    $euName = DB::table('enduser')->where('EndUserID', $parsedEndUserId)->value('FullName');
                    $resolvedParts[] = is_string($euName) && trim($euName) !== '' ? trim($euName) : $toUser;
                }
            } else {
                $resolvedParts[] = $toUser;
            }
        }

        if (!empty($resolvedParts)) {
            return trim(implode(' / ', array_filter($resolvedParts)));
        }

        $toDeptId = $details['to_department_id'] ?? null;
        $toEndUserId = $details['to_end_user_id'] ?? null;
        // Prefer human-readable department name and its head's full name when possible.
        $parts = [];

        if ($toDeptId) {
            $departmentName = DB::table('department')->where('DepartmentID', $toDeptId)->value('DepartmentName');
            $deptLabel = is_string($departmentName) && trim($departmentName) !== '' ? trim($departmentName) : ('Department #' . $toDeptId);

            // Find department head
            $headName = DB::table('user as u')
                ->leftJoin('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
                ->where('u.DepartmentID', $toDeptId)
                ->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
                ->orderByDesc('u.UserID')
                ->value(DB::raw("TRIM(COALESCE(u.FullName, ''))"));

            if (is_string($headName) && trim($headName) !== '') {
                $parts[] = $deptLabel . ' / ' . trim($headName);
            } else {
                $parts[] = $deptLabel;
            }
        }

        // If department id not provided but end user id is, attempt to derive department from enduser
        if (empty($parts) && $toEndUserId) {
            $endUserDeptId = DB::table('enduser')->where('EndUserID', $toEndUserId)->value('DepartmentID');
            if ($endUserDeptId) {
                $departmentName = DB::table('department')->where('DepartmentID', $endUserDeptId)->value('DepartmentName');
                $deptLabel = is_string($departmentName) && trim($departmentName) !== '' ? trim($departmentName) : ('Department #' . $endUserDeptId);

                $headName = DB::table('user as u')
                    ->leftJoin('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
                    ->where('u.DepartmentID', $endUserDeptId)
                    ->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
                    ->orderByDesc('u.UserID')
                    ->value(DB::raw("TRIM(COALESCE(u.FullName, ''))"));

                if (is_string($headName) && trim($headName) !== '') {
                    $parts[] = $deptLabel . ' / ' . trim($headName);
                } else {
                    $parts[] = $deptLabel;
                }
            }
        }

        if (!empty($parts)) {
            return trim(implode(' / ', $parts));
        }

        return 'Receiving office';
    }

    private function stringOrNull($value): ?string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }

        $text = trim((string) $value);
        return $text !== '' ? $text : null;
    }

    private function resolveDeptLabelFromId(int $departmentId): string
    {
        $departmentName = DB::table('department')->where('DepartmentID', $departmentId)->value('DepartmentName');
        $deptLabel = is_string($departmentName) && trim($departmentName) !== '' ? trim($departmentName) : ('Department #' . $departmentId);

        $headName = DB::table('user as u')
            ->leftJoin('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
            ->where('u.DepartmentID', $departmentId)
            ->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
            ->orderByDesc('u.UserID')
            ->value(DB::raw("TRIM(COALESCE(u.FullName, ''))"));

        if (is_string($headName) && trim($headName) !== '') {
            return $deptLabel . ' / ' . trim($headName);
        }

        return $deptLabel;
    }

    private function resolveIssuanceRemarks(int $issuanceId, int $inventoryDetailId): string
    {
        if ($issuanceId > 0) {
            $itemRemarks = DB::table('propertyissuancedetail')
                ->where('IssuanceID', $issuanceId)
                ->where('InventoryDetailID', $inventoryDetailId)
                ->whereNull('deleted_at')
                ->value('Remarks');

            $itemRemarksText = $this->stringOrNull($itemRemarks);
            if ($itemRemarksText !== null) {
                return $itemRemarksText;
            }

            $headerRemarks = DB::table('propertyissuance')
                ->where('IssuanceID', $issuanceId)
                ->whereNull('deleted_at')
                ->value('Remarks');

            $headerRemarksText = $this->stringOrNull($headerRemarks);
            if ($headerRemarksText !== null) {
                return $headerRemarksText;
            }
        }

        return 'Issued for accountability';
    }

    private function resolveTransferRemarks(int $transferId, int $inventoryDetailId): string
    {
        if ($transferId > 0) {
            $itemRemarks = DB::table('propertytransferdetails')
                ->where('TransferID', $transferId)
                ->where('InventoryDetailID', $inventoryDetailId)
                ->whereNull('deleted_at')
                ->value('Remarks');

            $itemRemarksText = $this->stringOrNull($itemRemarks);
            if ($itemRemarksText !== null) {
                return $itemRemarksText;
            }

            $reason = DB::table('propertytransfer')
                ->where('TransferID', $transferId)
                ->whereNull('deleted_at')
                ->value('Reason');

            $reasonText = $this->stringOrNull($reason);
            if ($reasonText !== null) {
                return $reasonText;
            }
        }

        return 'Transferred to receiving office';
    }

    private function resolveBorrowRemarks(int $borrowId, int $inventoryDetailId): string
    {
        if ($borrowId > 0) {
            $itemRemarks = DB::table('propertyborrowitems')
                ->where('BorrowID', $borrowId)
                ->where('InventoryDetailID', $inventoryDetailId)
                ->whereNull('deleted_at')
                ->value('Remarks');

            $itemRemarksText = $this->stringOrNull($itemRemarks);
            if ($itemRemarksText !== null) {
                return $itemRemarksText;
            }

            $purpose = DB::table('propertyborrow')
                ->where('BorrowID', $borrowId)
                ->whereNull('deleted_at')
                ->value('Purpose');

            $purposeText = $this->stringOrNull($purpose);
            if ($purposeText !== null) {
                return $purposeText;
            }
        }

        return 'Released under borrow transaction';
    }

    private function resolveBorrowReturnReference(int $returnId): ?string
    {
        if ($returnId <= 0) {
            return null;
        }

        $returnNumber = DB::table('propertyreturn')
            ->where('ReturnID', $returnId)
            ->whereNull('deleted_at')
            ->value('ReturnNumber');

        return $this->stringOrNull($returnNumber);
    }

    private function resolveBorrowReturnRemarks(int $returnId, int $inventoryDetailId): string
    {
        if ($returnId > 0) {
            $itemRemarks = DB::table('propertyreturnitems')
                ->where('ReturnID', $returnId)
                ->where('InventoryDetailID', $inventoryDetailId)
                ->whereNull('deleted_at')
                ->value('Remarks');

            $itemRemarksText = $this->stringOrNull($itemRemarks);
            if ($itemRemarksText !== null) {
                return $itemRemarksText;
            }

            $notes = DB::table('propertyreturn')
                ->where('ReturnID', $returnId)
                ->whereNull('deleted_at')
                ->value('Notes');

            $notesText = $this->stringOrNull($notes);
            if ($notesText !== null) {
                return $notesText;
            }
        }

        return 'Returned from borrow transaction';
    }

    private function resolveDisposalRemarks(array $details): string
    {
        $directReason = $this->stringOrNull($details['disposal_reason'] ?? null);
        if ($directReason !== null) {
            return $directReason;
        }

        $wasteNumber = $this->stringOrNull($details['waste_number'] ?? null);
        if ($wasteNumber !== null) {
            $notes = DB::table('property_waste_reports')
                ->where('WasteNumber', $wasteNumber)
                ->whereNull('deleted_at')
                ->value('Notes');

            $notesText = $this->stringOrNull($notes);
            if ($notesText !== null) {
                return $notesText;
            }
        }

        return 'Disposed item';
    }
}
