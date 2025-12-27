<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class StorageVerifyCommand extends Command
{
    protected $signature = 'storage:verify';

    protected $description = 'Verify configured storage driver is writable/readable';

    public function handle()
    {
        $disk = config('filesystems.default');
        $this->info("Checking storage disk: {$disk}");

        try {
            $tmpPath = 'signatures/.verify_' . time() . '.txt';
            Storage::disk($disk)->put($tmpPath, 'verify');
            if (!Storage::disk($disk)->exists($tmpPath)) {
                $this->error('Failed to write to disk');
                return 1;
            }
            $contents = Storage::disk($disk)->get($tmpPath);
            if ($contents !== 'verify') {
                $this->error('Read content mismatch');
                return 1;
            }
            Storage::disk($disk)->delete($tmpPath);
            $this->info('Storage verified successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Storage verify failed: ' . $e->getMessage());
            return 1;
        }
    }
}
