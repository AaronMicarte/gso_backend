<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreIarSignatureRequest;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\IarSignature;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IarSignatureController extends Controller
{
    public function store(StoreIarSignatureRequest $request, $iarId)
    {
        $iar = InspectionAcceptanceReport::find($iarId);
        if (!$iar) {
            return response()->json(['message' => 'IAR not found'], 404);
        }

        $role = $request->input('role');
        // current authenticated user
        $currentUserId = auth()->user()->UserID ?? null;
        // expected signer: prefer assigned user on record, but fall back to current authenticated user
        if ($role === 'inspected') {
            $expectedUserId = $iar->InspectedBy ?? $currentUserId;
        } else {
            $expectedUserId = $iar->AcceptedBy ?? $currentUserId;
        }

        if (!$expectedUserId) {
            return response()->json(['message' => 'No signer assigned for this role'], 422);
        }

        if ($currentUserId != $expectedUserId) {
            return response()->json(['message' => 'Authenticated user must match selected signer for this role'], 403);
        }

        // Delegate processing + storage to SignatureService
        $file = $request->file('signature');
        $source = $request->input('source') ?? 'upload';
        // resolve from container so tests can override behavior
        $this->signatureService = app(\App\Services\SignatureService::class);

        try {
            $input = $file ?? $request->input('signature');
            $signature = $this->signatureService->processAndStore($iar->IARID, $role, $input, $source, $currentUserId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Signature saved', 'data' => $signature], 201);
    }

    /**
     * Stream or redirect to the stored signature file.
     * This is a safe fallback when the storage URL is not directly accessible via web server.
     */
    public function show($iarId, $role)
    {
        $signature = IarSignature::where('iar_id', $iarId)->where('role', $role)->first();
        if (!$signature) {
            return response()->json(['message' => 'Signature not found'], 404);
        }

        // For S3 we redirect to the temporary URL (SignatureService would have generated one)
        if ($signature->storage_disk === 's3' && $signature->url) {
            return redirect($signature->url);
        }

        $disk = $signature->storage_disk ?? config('filesystems.default', 'public');

        if (!Storage::disk($disk)->exists($signature->storage_path)) {
            return response()->json(['message' => 'Signature file not found'], 404);
        }

        // Stream the file with inline disposition so browsers can display it as an image
        return Storage::disk($disk)->response($signature->storage_path, null, [
            'Cache-Control' => 'public, max-age=31536000',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Public stream for local/dev disk signatures.
     * Only available for signatures stored on the 'local' disk (private storage root).
     * This route is intentionally public for development convenience; it will return 403 for non-local disks.
     */
    public function showPublic($iarId, $role)
    {
        $signature = IarSignature::where('iar_id', $iarId)->where('role', $role)->first();
        if (!$signature) {
            return response()->json(['message' => 'Signature not found'], 404);
        }

        // Only allow public streaming for local disk in dev environment or when disk === 'local'
        $disk = $signature->storage_disk ?? config('filesystems.default', 'public');
        if ($disk !== 'local') {
            return response()->json(['message' => 'Public streaming not allowed for this signature'], 403);
        }

        if (!Storage::disk($disk)->exists($signature->storage_path)) {
            return response()->json(['message' => 'Signature file not found'], 404);
        }

        return Storage::disk($disk)->response($signature->storage_path, null, [
            'Cache-Control' => 'public, max-age=31536000',
            'Pragma' => 'public',
        ]);
    }
}
