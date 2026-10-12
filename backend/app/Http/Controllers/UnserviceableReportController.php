<?php

namespace App\Http\Controllers;

use App\Models\AssetAuditLog;
use App\Models\AssignedProperty;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\PropertyStatus;
use App\Models\PropertyUnserviceableAttachment;
use App\Models\PropertyUnserviceableItem;
use App\Models\PropertyUnserviceableReport;
use App\Models\PropertyWasteItem;
use App\Models\PropertyWasteReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UnserviceableReportController extends Controller
{
    private const UNSERVICEABLE_CONDITION_ID = 2;

    /**
     * Store a new unserviceable item report with proof attachments.
     */
    public function store(Request $request): JsonResponse
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId <= 0) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Gather all uploaded files (from attachments[] or attachment or files[])
        $uploadedFiles = [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            $uploadedFiles = is_array($files) ? $files : [$files];
        } elseif ($request->hasFile('attachment')) {
            $uploadedFiles = [$request->file('attachment')];
        } elseif ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = is_array($files) ? $files : [$files];
        }

        // Validate proof attachments requirement: at least one supporting document or image
        if (empty($uploadedFiles)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'attachments' => ['At least one supporting document or image (proof) is required.'],
                ],
            ], 422);
        }

        // Validate each attachment type and size (PDF, JPG, JPEG, PNG, WEBP; max 10MB)
        $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        $maxSizeBytes = 10 * 1024 * 1024; // 10MB

        foreach ($uploadedFiles as $index => $file) {
            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        "attachments.{$index}" => ['Uploaded file is invalid or corrupted.'],
                    ],
                ], 422);
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedMimes, true)) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        "attachments.{$index}" => ['Invalid file format. Allowed formats: PDF, JPG, JPEG, PNG, WEBP.'],
                    ],
                ], 422);
            }

            if ($file->getSize() > $maxSizeBytes) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        "attachments.{$index}" => ['File size exceeds the 10MB limit.'],
                    ],
                ], 422);
            }
        }

        // Decode item IDs
        $itemIdsInput = $request->input('items');
        if (is_string($itemIdsInput)) {
            $decoded = json_decode($itemIdsInput, true);
            if (is_array($decoded)) {
                $itemIdsInput = $decoded;
            }
        }

        if (empty($itemIdsInput) || !is_array($itemIdsInput)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'items' => ['At least one inventory item must be selected.'],
                ],
            ], 422);
        }

        $normalizedItemIds = [];
        foreach ($itemIdsInput as $item) {
            if (is_numeric($item)) {
                $normalizedItemIds[] = (int) $item;
            } elseif (is_array($item) && isset($item['inventory_detail_id']) && is_numeric($item['inventory_detail_id'])) {
                $normalizedItemIds[] = (int) $item['inventory_detail_id'];
            } elseif (is_array($item) && isset($item['InventoryDetailID']) && is_numeric($item['InventoryDetailID'])) {
                $normalizedItemIds[] = (int) $item['InventoryDetailID'];
            }
        }

        $normalizedItemIds = array_values(array_unique(array_filter($normalizedItemIds, fn($id) => $id > 0)));

        if (empty($normalizedItemIds)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'items' => ['Valid inventory items are required.'],
                ],
            ], 422);
        }

        // Retrieve items from DB
        $inventoryDetails = PropertyInventoryDetail::with([
            'inventory',
            'drDetail.article',
            'condition',
        ])->whereIn('InventoryDetailID', $normalizedItemIds)->get();

        if ($inventoryDetails->count() !== count($normalizedItemIds)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'items' => ['One or more selected inventory items could not be found.'],
                ],
            ], 422);
        }

        // If the asset is serviceable again, resolve orphaned active reports before duplicate checks.
        foreach ($inventoryDetails as $detail) {
            PropertyUnserviceableReport::reconcileActiveReportsForServiceableInventory(
                $detail,
                $userId,
                ['trigger' => 'unserviceable_report_store']
            );
        }

        // Block only when an active unserviceable report already exists for the item.
        foreach ($inventoryDetails as $detail) {
            $activeReportItem = PropertyUnserviceableItem::query()
                ->forInventoryDetail((int) $detail->InventoryDetailID)
                ->withActiveReport()
                ->with('report')
                ->first();

            $itemIdentifier = $detail->PropertyNumber ?: ($detail->drDetail?->article?->ArticleName ?: "Item #{$detail->InventoryDetailID}");

            if ($activeReportItem && $activeReportItem->report) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        'items' => ["{$itemIdentifier} already has an active unserviceable report ({$activeReportItem->report->report_number})."],
                    ],
                ], 422);
            }
        }

        $remarks = trim((string) $request->input('remarks', ''));

        // Execute persistence inside database transaction
        return DB::transaction(function () use ($uploadedFiles, $inventoryDetails, $remarks, $userId) {
            $reportNumber = PropertyUnserviceableReport::generateReportNumber();

            // Save primary file metadata for the report record
            $firstFile = $uploadedFiles[0];
            $primaryStoredPath = $firstFile->store('unserviceable_reports', 'local');

            $report = PropertyUnserviceableReport::create([
                'report_number' => $reportNumber,
                'status' => PropertyUnserviceableReport::STATUS_ACTIVE,
                'remarks' => $remarks !== '' ? $remarks : null,
                'created_by_user_id' => $userId,
                'attachment_path' => $primaryStoredPath,
                'attachment_name' => $firstFile->getClientOriginalName(),
                'attachment_mime' => $firstFile->getClientMimeType() ?: $firstFile->getMimeType(),
                'attachment_size' => $firstFile->getSize(),
            ]);

            // Save each file in attachments table
            foreach ($uploadedFiles as $index => $file) {
                $filePath = ($index === 0) ? $primaryStoredPath : $file->store('unserviceable_reports', 'local');

                PropertyUnserviceableAttachment::create([
                    'unserviceable_report_id' => $report->id,
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_mime' => $file->getClientMimeType() ?: $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Associate items and update condition (preserving existing PropertyStatus e.g. Issued)
            foreach ($inventoryDetails as $detail) {
                $description = $detail->Description
                    ?: ($detail->drDetail?->article?->ArticleName ?: "Asset #{$detail->InventoryDetailID}");

                PropertyUnserviceableItem::create([
                    'unserviceable_report_id' => $report->id,
                    'inventory_detail_id' => $detail->InventoryDetailID,
                    'property_number' => $detail->PropertyNumber,
                    'item_description' => $description,
                    'remarks' => $remarks !== '' ? $remarks : null,
                ]);

                // Update item condition to Unserviceable (keep status e.g. Issued)
                $detail->ConditionID = self::UNSERVICEABLE_CONDITION_ID;
                $detail->save();

                // Sync AssignedProperty
                AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->update([
                    'ConditionID' => self::UNSERVICEABLE_CONDITION_ID,
                ]);

                // Audit log & Property Card ledger sync
                AssetAuditLog::createLog(
                    'inventory_detail',
                    $detail->InventoryDetailID,
                    'reported_unserviceable',
                    $userId,
                    [
                        'report_number' => $report->report_number,
                        'remarks' => $remarks,
                        'attachments_count' => count($uploadedFiles),
                    ]
                );
            }

            return response()->json([
                'message' => 'Unserviceable report submitted successfully',
                'report' => $report->load(['items', 'attachments', 'createdBy:UserID,FullName']),
            ], 201);
        });
    }

    /**
     * Get unserviceable report and proof information for a specific inventory detail.
     */
    public function showForDetail(Request $request, int $detailId): JsonResponse
    {
        // 1. Direct match for this specific inventory detail
        $item = PropertyUnserviceableItem::query()
            ->forInventoryDetail($detailId)
            ->withActiveReport()
            ->with([
                'report.createdBy:UserID,FullName',
                'report.attachments',
                'inventoryDetail',
            ])
            ->latest('id')
            ->first();

        // 2. If not found, check sibling items in the same inventory / batch
        if (!$item) {
            $siblingIds = [];
            if ($request->filled('detail_ids')) {
                $siblingIds = array_filter(array_map('intval', explode(',', (string) $request->query('detail_ids'))));
            } else {
                $currentDetail = PropertyInventoryDetail::find($detailId);
                if ($currentDetail) {
                    $siblingIds = PropertyInventoryDetail::where('InventoryID', $currentDetail->InventoryID)
                        ->where(function ($q) use ($currentDetail) {
                            if (!empty($currentDetail->SourceDRDetailID)) {
                                $q->where('SourceDRDetailID', $currentDetail->SourceDRDetailID);
                            } elseif (!empty($currentDetail->PropertyTypeID)) {
                                $q->where('PropertyTypeID', $currentDetail->PropertyTypeID);
                            }
                        })
                        ->pluck('InventoryDetailID')
                        ->all();
                }
            }

            if (!empty($siblingIds)) {
                $item = PropertyUnserviceableItem::query()
                    ->whereIn('inventory_detail_id', $siblingIds)
                    ->withActiveReport()
                    ->with([
                        'report.createdBy:UserID,FullName',
                        'report.attachments',
                        'inventoryDetail',
                    ])
                    ->latest('id')
                    ->first();
            }
        }

        if (!$item || !$item->report) {
            return response()->json([
                'has_report' => false,
                'report' => null,
            ]);
        }

        $report = $item->report;
        $attachments = $report->attachments->map(function ($att) use ($report) {
            return [
                'id' => $att->id,
                'file_name' => $att->file_name,
                'file_mime' => $att->file_mime,
                'file_size' => $att->file_size,
                'is_image' => str_starts_with((string) $att->file_mime, 'image/'),
                'is_pdf' => (string) $att->file_mime === 'application/pdf' || str_ends_with(strtolower($att->file_name), '.pdf'),
                'view_url' => url("/api/inventory/unserviceable-reports/{$report->id}/attachments/{$att->id}/view"),
                'download_url' => url("/api/inventory/unserviceable-reports/{$report->id}/attachments/{$att->id}/download"),
            ];
        });

        // Fallback to primary attachment if attachments table was empty
        if ($attachments->isEmpty() && $report->attachment_path) {
            $isImg = str_starts_with((string) $report->attachment_mime, 'image/');
            $isPdf = (string) $report->attachment_mime === 'application/pdf' || str_ends_with(strtolower((string) $report->attachment_name), '.pdf');
            $attachments = collect([[
                'id' => 0,
                'file_name' => $report->attachment_name ?: 'proof_attachment',
                'file_mime' => $report->attachment_mime,
                'file_size' => $report->attachment_size,
                'is_image' => $isImg,
                'is_pdf' => $isPdf,
                'view_url' => url("/api/inventory/unserviceable-reports/{$report->id}/attachments/0/view"),
                'download_url' => url("/api/inventory/unserviceable-reports/{$report->id}/attachments/0/download"),
            ]]);
        }

        return response()->json([
            'has_report' => true,
            'report' => [
                'id' => $report->id,
                'report_number' => $report->report_number,
                'status' => $report->status,
                'remarks' => $report->remarks,
                'created_at' => $report->created_at?->toIso8601String(),
                'reported_by' => $report->createdBy ? [
                    'id' => $report->createdBy->UserID,
                    'name' => $report->createdBy->FullName,
                ] : null,
                'item_remarks' => $item->remarks,
                'affected_property_number' => $item->property_number,
                'affected_item_description' => $item->item_description,
                'attachments' => $attachments,
            ],
        ]);
    }

    /**
     * Authenticated view/stream of an attachment (inline display for preview).
     */
    public function viewAttachment(Request $request, int $reportId, int $attachmentId)
    {
        $attachment = $this->resolveAttachment($reportId, $attachmentId);
        if (!$attachment) {
            return response()->json(['message' => 'Attachment not found'], 404);
        }

        $path = $attachment['path'];
        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['message' => 'Attachment file missing on server'], 404);
        }

        $mime = $attachment['mime'] ?: Storage::disk('local')->mimeType($path) ?: 'application/octet-stream';
        $name = $attachment['name'];

        return Storage::disk('local')->response($path, $name, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Authenticated download of an attachment.
     */
    public function downloadAttachment(Request $request, int $reportId, int $attachmentId)
    {
        $attachment = $this->resolveAttachment($reportId, $attachmentId);
        if (!$attachment) {
            return response()->json(['message' => 'Attachment not found'], 404);
        }

        $path = $attachment['path'];
        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['message' => 'Attachment file missing on server'], 404);
        }

        return Storage::disk('local')->download($path, $attachment['name']);
    }

    /**
     * Resolve attachment metadata.
     */
    private function resolveAttachment(int $reportId, int $attachmentId): ?array
    {
        $report = PropertyUnserviceableReport::find($reportId);
        if (!$report) {
            return null;
        }

        if ($attachmentId > 0) {
            $att = PropertyUnserviceableAttachment::where('unserviceable_report_id', $reportId)
                ->where('id', $attachmentId)
                ->first();

            if ($att) {
                return [
                    'path' => $att->file_path,
                    'name' => $att->file_name,
                    'mime' => $att->file_mime,
                ];
            }
        }

        // Fallback to primary attachment on report
        if ($report->attachment_path) {
            return [
                'path' => $report->attachment_path,
                'name' => $report->attachment_name ?: 'proof_attachment',
                'mime' => $report->attachment_mime,
            ];
        }

        return null;
    }
}
