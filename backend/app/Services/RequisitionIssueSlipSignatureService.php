<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\RequisitionIssueSlipSignature;
use Illuminate\Http\UploadedFile;

class RequisitionIssueSlipSignatureService
{
    protected string $disk;
    protected bool $enforcePixelCoverage = true;

    public function __construct(bool $enforcePixelCoverage = true)
    {
        $this->disk = 'public';
        $this->enforcePixelCoverage = $enforcePixelCoverage;
    }

    /**
     * Process input (UploadedFile or base64) and store canonical PNG, return DB record.
     */
    public function processAndStore(int $risId, string $role, $signatureInput, string $source, int $signedByUserId)
    {
        $pngContents = $this->normalizeToPng($signatureInput);

        [$width, $height] = $this->getImageDimensions($pngContents);
        if ($this->enforcePixelCoverage && !$this->passesPixelCoverage($pngContents, $width, $height)) {
            throw new \Exception('Signature appears empty or too faint');
        }

        $filename = 'signatures/ris_' . $risId . '_' . $role . '_' . time() . '.png';
        Storage::disk($this->disk)->put($filename, $pngContents);

        $url = $this->getUrl($filename);
        $tmpPath = Storage::disk($this->disk)->path($filename);
        $hash = hash_file('sha256', $tmpPath);
        $size = Storage::disk($this->disk)->size($filename);

        $signature = RequisitionIssueSlipSignature::updateOrCreate(
            ['ris_id' => $risId, 'role' => $role],
            [
                'signed_by_user_id' => $signedByUserId,
                'source' => $source,
                'storage_disk' => $this->disk,
                'storage_path' => $filename,
                'url' => $url,
                'mime' => 'image/png',
                'width' => $width,
                'height' => $height,
                'size_bytes' => $size,
                'sha256_hash' => $hash,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );

        return $signature;
    }

    protected function getUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    protected function normalizeToPng($signatureInput): string
    {
        if ($signatureInput instanceof UploadedFile) {
            $original = file_get_contents($signatureInput->getRealPath());
            $detected = $signatureInput->getClientMimeType();
        } else {
            if (!is_string($signatureInput) || !preg_match('/^data:image\/(png|jpeg);base64,/', $signatureInput)) {
                throw new \Exception('No valid signature data');
            }
            $base64 = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $signatureInput);
            $decoded = base64_decode($base64);
            if ($decoded === false) {
                throw new \Exception('Invalid base64');
            }
            $original = $decoded;
            $finfo = finfo_open();
            $detected = finfo_buffer($finfo, $decoded, FILEINFO_MIME_TYPE);
            finfo_close($finfo);
        }

        if (!in_array($detected, ['image/png', 'image/jpeg'])) {
            throw new \Exception('Invalid image type');
        }

        if ($detected === 'image/jpeg') {
            if (!function_exists('imagecreatefromstring')) {
                throw new \Exception('Cannot convert JPEG to PNG: GD extension not available');
            }
            $img = imagecreatefromstring($original);
            ob_start();
            imagepng($img);
            $pngContents = ob_get_clean();
            imagedestroy($img);
            return $pngContents;
        }

        return $original;
    }

    protected function getImageDimensions(string $pngContents): array
    {
        if (function_exists('imagecreatefromstring')) {
            $image = imagecreatefromstring($pngContents);
            if (!$image) {
                throw new \Exception('Invalid image data');
            }
            $width = imagesx($image);
            $height = imagesy($image);
            imagedestroy($image);
            return [$width, $height];
        }

        if (substr($pngContents, 0, 8) === "\x89PNG\r\n\x1a\n") {
            $ihdr = substr($pngContents, 8, 25);
            if (strlen($ihdr) >= 25 && substr($ihdr, 4, 4) === 'IHDR') {
                $width = unpack('N', substr($ihdr, 8, 4))[1];
                $height = unpack('N', substr($ihdr, 12, 4))[1];
                return [$width, $height];
            }
        }

        throw new \Exception('Cannot determine image dimensions without GD');
    }

    protected function passesPixelCoverage(string $pngContents, int $width, int $height): bool
    {
        if (function_exists('imagecreatefromstring')) {
            $image = imagecreatefromstring($pngContents);
            $nonEmpty = 0;
            $samples = 0;

            $stepX = max(1, (int)($width / 100));
            $stepY = max(1, (int)($height / 50));

            for ($x = 0; $x < $width; $x += $stepX) {
                for ($y = 0; $y < $height; $y += $stepY) {
                    $rgb = imagecolorat($image, $x, $y);
                    $colors = imagecolorsforindex($image, $rgb);
                    if ($colors['red'] < 250 || $colors['green'] < 250 || $colors['blue'] < 250) {
                        $nonEmpty++;
                    }
                    $samples++;
                }
            }

            imagedestroy($image);

            $fraction = $samples ? ($nonEmpty / $samples) : 0;
            return $fraction >= 0.005;
        }

        return strlen($pngContents) > 200;
    }
}
