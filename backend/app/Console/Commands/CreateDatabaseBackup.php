<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CreateDatabaseBackup extends Command
{
    protected $signature = 'backup:run {--disk=} {--directory=} {--origin=scheduled}';

    protected $description = 'Create a database backup (used by scheduler)';

    public function handle()
    {
        $disk = $this->option('disk') ?: config('backup.disk', config('filesystems.default'));
        $dir = $this->option('directory') ?: config('backup.directory', 'backups');
        $origin = $this->option('origin') ?: 'scheduled';

        try {
            Storage::disk($disk)->makeDirectory($dir);

            $db = config('database.connections.' . config('database.default'));

            $dbName = $db['database'] ?? env('DB_DATABASE');
            $user = $db['username'] ?? env('DB_USERNAME');
            $pass = $db['password'] ?? env('DB_PASSWORD');
            $host = $db['host'] ?? env('DB_HOST', '127.0.0.1');
            $port = $db['port'] ?? env('DB_PORT', '3306');

            $timestamp = date('Ymd_His');
            $filename = "backup_{$timestamp}_{$dbName}.sql";
            $tmpPath = storage_path('app/' . $dir . DIRECTORY_SEPARATOR . $filename);

            $cmd = sprintf(
                'mysqldump --routines --single-transaction -h %s -P %s -u %s %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($user),
                escapeshellarg($dbName)
            );

            if (!empty($pass)) {
                putenv('MYSQL_PWD=' . $pass);
            }

            $cmd .= ' 2>&1 > ' . escapeshellarg($tmpPath);

            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($tmpPath)) {
                if (file_exists($tmpPath)) {@unlink($tmpPath);} 
                $this->error('Failed to create backup: ' . implode("\n", $output));
                return 1;
            }

            @chmod($tmpPath, 0600);

            // Move to disk if not local
            if ($disk !== 'local') {
                $relPath = $dir . '/' . $filename;
                Storage::disk($disk)->put($relPath, fopen($tmpPath, 'r'));
                @unlink($tmpPath);
            }

            // Write audit log (performed by system)
            try {
                DB::table('asset_audit_logs')->insert([
                    'EntityType' => 'database_backup',
                    'EntityID' => 0,
                    'Action' => 'created',
                    'PerformedBy' => null,
                    'Details' => json_encode(['file' => $filename, 'origin' => $origin, 'disk' => $disk]),
                    'IPAddress' => null,
                    'UserAgent' => 'scheduler',
                    'CreatedAt' => now()->toDateTimeString(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to write backup audit: ' . $e->getMessage());
            }

            $deleted = $this->purgeOldBackups($disk, $dir);
            if (!empty($deleted)) {
                foreach ($deleted as $deletedFile) {
                    try {
                        DB::table('asset_audit_logs')->insert([
                            'EntityType' => 'database_backup',
                            'EntityID' => 0,
                            'Action' => 'auto_deleted',
                            'PerformedBy' => null,
                            'Details' => json_encode(['file' => $deletedFile, 'reason' => 'retention']),
                            'IPAddress' => null,
                            'UserAgent' => 'scheduler',
                            'CreatedAt' => now()->toDateTimeString(),
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to write backup audit for deleted file: ' . $e->getMessage());
                    }
                }
            }

            $this->info('Backup created: ' . $filename);
            return 0;
        } catch (\Exception $e) {
            $this->error('Exception creating backup: ' . $e->getMessage());
            return 1;
        }
    }

    private function purgeOldBackups(string $disk, string $dir): array
    {
        $retentionDays = (int) config('backup.retention_days', 30);
        if ($retentionDays <= 0) {
            return [];
        }

        $cutoff = Carbon::now()->subDays($retentionDays)->timestamp;
        $deleted = [];

        foreach (Storage::disk($disk)->files($dir) as $path) {
            if (!Str::endsWith($path, '.sql') && !Str::endsWith($path, '.gz')) {
                continue;
            }

            try {
                $modified = Storage::disk($disk)->lastModified($path);
            } catch (\Exception $e) {
                continue;
            }

            if ($modified !== null && $modified < $cutoff) {
                Storage::disk($disk)->delete($path);
                $deleted[] = basename($path);
            }
        }

        return $deleted;
    }
}
