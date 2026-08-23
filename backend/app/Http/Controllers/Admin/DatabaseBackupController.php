<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseBackupController extends Controller
{
    private $backupDir = 'backups';

    public function index(Request $request)
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);
        
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        
        $disk = config('backup.disk', 'local');
        $dir = config('backup.directory', $this->backupDir);

        try {
            $files = Storage::disk($disk)->exists($dir)
                ? Storage::disk($disk)->files($dir)
                : [];
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to list backups',
                'details' => $e->getMessage(),
            ], 500);
        }

        $backups = collect($files)
            ->filter(function ($f) {
                return Str::endsWith($f, '.sql') || Str::endsWith($f, '.gz');
            })
            ->map(function ($path) use ($disk) {
                return [
                    'path' => $path,
                    'name' => basename($path),
                    'size' => Storage::disk($disk)->size($path),
                    'modified' => Storage::disk($disk)->lastModified($path),
                ];
            })->values();

        $auditMap = $this->getCreatedBackupAudits($backups->pluck('name')->all());

        $list = $backups->map(function ($backup) use ($auditMap) {
            $audit = isset($auditMap[$backup['name']]) ? $auditMap[$backup['name']] : null;
            return array_merge($backup, [
                'created_by' => $audit['performed_by'] ?? 'System',
                'status' => 'Success',
                'created_at' => $audit['created_at'] ?? $backup['modified'],
            ]);
        })->sortByDesc('modified')->values();

        // Paginate the results
        $total = $list->count();
        $paginated = $list->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'data' => $paginated,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $disk = config('backup.disk', 'local');
        $dir = config('backup.directory', $this->backupDir);

        // Create backups directory if missing
        Storage::disk($disk)->makeDirectory($dir);

        $db = config('database.connections.' . config('database.default'));

        $dbName = $db['database'] ?? env('DB_DATABASE');
        $user = $db['username'] ?? env('DB_USERNAME');
        $pass = $db['password'] ?? env('DB_PASSWORD');
        $host = $db['host'] ?? env('DB_HOST', '127.0.0.1');
        $port = $db['port'] ?? env('DB_PORT', '3306');

       $date = date('Y-m-d');
        $time = date('g.iA');
        $filename = "gso_backup_{$date}_{$time}.sql";
        $tmpPathLocal = storage_path('app/' . $dir . DIRECTORY_SEPARATOR . $filename);

        // Try mysqldump command first
        $cnfFile = tempnam(sys_get_temp_dir(), 'mysqldump');
        file_put_contents($cnfFile, "[mysqldump]\npassword=" . $pass . "\n");

        $cmd = sprintf(
            '"C:\\xampp\\mysql\\bin\\mysqldump.exe" --defaults-extra-file=%s --routines --single-transaction -h %s -P %s -u %s %s',
            escapeshellarg($cnfFile),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($dbName)
        );

        $errLog = storage_path('logs/mysqldump_err.txt');
        $cmd .= ' > ' . escapeshellarg($tmpPathLocal) . ' 2>' . escapeshellarg($errLog);

        exec($cmd, $output, $returnVar);

        return response()->json([
    'cmd' => $cmd,
    'return_var' => $returnVar,
    'output' => $output,
    'file_exists' => file_exists($tmpPathLocal),
    'filesize' => file_exists($tmpPathLocal) ? filesize($tmpPathLocal) : null,
    'err_log' => file_exists($errLog) ? file_get_contents($errLog) : 'no err log',
]);

        @unlink($cnfFile);

        if ($returnVar !== 0 || !file_exists($tmpPathLocal) || filesize($tmpPathLocal) === 0) {
            if (file_exists($tmpPathLocal)) {
                @unlink($tmpPathLocal);
            }
            $errDetail = file_exists($errLog) ? file_get_contents($errLog) : implode("\n", $output);
            return response()->json([
                'message' => 'Failed to create database backup',
                'details' => $errDetail,
            ], 500);
        }
        // Try to restrict file permissions when possible
        @chmod($tmpPathLocal, 0600);

        // If using a non-local disk, move it
        if ($disk !== 'local') {
            $relPath = $dir . '/' . $filename;
            Storage::disk($disk)->put($relPath, fopen($tmpPathLocal, 'r'));
            @unlink($tmpPathLocal);
        }

        // Log audit
        $this->writeAudit($request, 'database_backup', 'created', ['file' => $filename, 'disk' => $disk]);

        $deleted = $this->purgeOldBackups($disk, $dir);
        if (!empty($deleted)) {
            foreach ($deleted as $deletedFile) {
                $this->writeAudit($request, 'database_backup', 'auto_deleted', ['file' => $deletedFile, 'reason' => 'retention']);
            }
        }

        return response()->json(['message' => 'Backup created', 'file' => $filename, 'deleted' => $deleted]);
    }

    public function status(Request $request)
    {
        $schedule = config('backup.schedule', 'disabled');
        $preferredTime = config('backup.time', '00:00');
        $retentionDays = (int) config('backup.retention_days', 30);
        $nextBackup = $this->getNextScheduledBackup($schedule, $preferredTime);

        return response()->json([
            'frequency' => $schedule,
            'time' => $preferredTime,
            'next_backup' => $nextBackup ? $nextBackup->toIso8601String() : null,
            'retention_days' => $retentionDays,
            'status' => $schedule === 'disabled' ? 'disabled' : 'active',
        ]);
    }

    public function settings(Request $request)
    {
        $schedule = config('backup.schedule', 'disabled');
        $preferredTime = config('backup.time', '00:00');
        $retentionDays = (int) config('backup.retention_days', 30);

        return response()->json([
            'frequency' => $schedule,
            'time' => $preferredTime,
            'retention_days' => $retentionDays,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'frequency' => 'required|in:disabled,daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'retention_days' => 'nullable|integer|min:0',
        ]);

        $payload = [
            'schedule' => $data['frequency'],
            'time' => $data['time'],
        ];

        if (isset($data['retention_days'])) {
            $payload['retention_days'] = (int) $data['retention_days'];
        }

        $settingsPath = storage_path('app/backup-settings.json');
        file_put_contents($settingsPath, json_encode($payload, JSON_PRETTY_PRINT));

        $this->writeAudit($request, 'database_backup', 'schedule_updated', $payload);

        return response()->json([
            'message' => 'Backup schedule settings updated',
            'data' => $payload,
        ]);
    }

    public function download(Request $request, $name)
    {
        $disk = config('backup.disk', 'local');
        $dir = config('backup.directory', $this->backupDir);

        $path = $dir . '/' . $name;

        if (!Storage::disk($disk)->exists($path)) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $this->writeAudit($request, 'database_backup', 'downloaded', ['file' => $name]);

        return Storage::disk($disk)->download($path, $name);
    }

    public function destroy(Request $request, $name)
    {
        $disk = config('backup.disk', 'local');
        $dir = config('backup.directory', $this->backupDir);

        $path = $dir . '/' . $name;

        if (!Storage::disk($disk)->exists($path)) {
            return response()->json(['message' => 'Not found'], 404);
        }

        Storage::disk($disk)->delete($path);

        $this->writeAudit($request, 'database_backup', 'deleted', ['file' => $name]);

        return response()->json(['message' => 'Deleted']);
    }

    public function restore(Request $request, $name)
    {
        // Restore via web is disabled by default for safety. Enable with config('backup.allow_web_restore')
        if (!config('backup.allow_web_restore', false)) {
            return response()->json(['message' => 'Web restore is disabled'], 403);
        }

        $path = storage_path('app/' . $this->backupDir . '/' . $name);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Restoring is destructive: drop & import. We'll import using mysql client if available.
        $db = config('database.connections.' . config('database.default'));
        $dbName = $db['database'] ?? env('DB_DATABASE');
        $user = $db['username'] ?? env('DB_USERNAME');
        $pass = $db['password'] ?? env('DB_PASSWORD');
        $host = $db['host'] ?? env('DB_HOST', '127.0.0.1');
        $port = $db['port'] ?? env('DB_PORT', '3306');

        // Attempt to run mysql to import
        $cmd = sprintf('mysql -h %s -P %s -u %s %s', escapeshellarg($host), escapeshellarg($port), escapeshellarg($user), escapeshellarg($dbName));

        if (!empty($pass)) {
            $cmd = 'MYSQL_PWD=' . escapeshellarg($pass) . ' ' . $cmd;
        }

        $cmd .= ' < ' . escapeshellarg($path) . ' 2>&1';

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['message' => 'Restore failed', 'details' => implode("\n", $output)], 500);
        }

        $this->writeAudit($request, 'database_backup', 'restored', ['file' => $name]);

        return response()->json(['message' => 'Restore completed']);
    }

    public function triggerScheduledBackup(Request $request)
    {
        $schedule = config('backup.schedule', 'disabled');
        $preferredTime = config('backup.time', '00:00');

        if ($schedule === 'disabled') {
            return response()->json([
                'message' => 'Scheduled backups are disabled',
                'executed' => false,
                'reason' => 'Schedule is set to disabled',
            ]);
        }

        // Check if current time is past or at the scheduled time (within a 5-minute window)
        $now = Carbon::now();
        [$hour, $minute] = explode(':', $preferredTime) + [0, 0];
        $hour = (int) $hour;
        $minute = (int) $minute;

        $scheduledTime = $now->copy()->setTime($hour, $minute, 0);
        $windowStart = $scheduledTime->copy()->subMinutes(5);
        $windowEnd = $scheduledTime->copy()->addMinutes(5);

        $isDue = false;
        if ($schedule === 'daily') {
            $isDue = $now->isBetween($windowStart, $windowEnd);
        } elseif ($schedule === 'weekly') {
            // Check if today is Sunday and within the time window
            $isDue = $now->dayOfWeek === 0 && $now->isBetween($windowStart, $windowEnd);
        } elseif ($schedule === 'monthly') {
            // Check if today is the 1st of the month and within the time window
            $isDue = $now->day === 1 && $now->isBetween($windowStart, $windowEnd);
        }

        if (!$isDue) {
            $nextBackup = $this->getNextScheduledBackup($schedule, $preferredTime);
            return response()->json([
                'message' => 'Backup is not currently due',
                'executed' => false,
                'schedule' => $schedule,
                'scheduled_time' => $preferredTime,
                'next_backup' => $nextBackup ? $nextBackup->toIso8601String() : null,
                'current_time' => $now->toIso8601String(),
            ]);
        }

        // Execute the scheduled backup command
        try {
            ob_start();
            $exitCode = Artisan::call('backup:run', ['--origin' => 'manual']);
            $output = ob_get_clean();

            $nextBackup = $this->getNextScheduledBackup($schedule, $preferredTime);

            $this->writeAudit($request, 'database_backup', 'scheduled_executed', [
                'schedule' => $schedule,
                'trigger' => 'manual_api',
                'exit_code' => $exitCode,
            ]);

            return response()->json([
                'message' => 'Scheduled backup executed successfully',
                'executed' => true,
                'exit_code' => $exitCode,
                'schedule' => $schedule,
                'scheduled_time' => $preferredTime,
                'next_backup' => $nextBackup ? $nextBackup->toIso8601String() : null,
                'output' => $output ?: 'Backup completed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to execute scheduled backup',
                'executed' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getCreatedBackupAudits(array $fileNames): array
    {
        if (empty($fileNames)) {
            return [];
        }

        $query = DB::table('asset_audit_logs')
            ->leftJoin('user', 'asset_audit_logs.PerformedBy', '=', 'user.UserID')
            ->select('asset_audit_logs.Details', 'asset_audit_logs.CreatedAt', 'user.FullName as performed_by')
            ->where('asset_audit_logs.EntityType', 'database_backup')
            ->where('asset_audit_logs.Action', 'created');

        $query->where(function ($q) use ($fileNames) {
            foreach ($fileNames as $fileName) {
                $q->orWhere('asset_audit_logs.Details', 'like', '%' . $fileName . '%');
            }
        });

        $records = $query->get();

        $map = [];
        foreach ($records as $record) {
            $details = json_decode($record->Details, true);
            if (!is_array($details) || empty($details['file'])) {
                continue;
            }

            $fileName = $details['file'];
            if (!in_array($fileName, $fileNames, true)) {
                continue;
            }

            $map[$fileName] = [
                'performed_by' => $record->performed_by ?? 'System',
                'created_at' => $record->CreatedAt,
            ];
        }

        return $map;
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

    private function getNextScheduledBackup(string $schedule, string $time): ?Carbon
    {
        if ($schedule === 'disabled') {
            return null;
        }

        $now = Carbon::now();
        [$hour, $minute] = explode(':', $time) + [0, 0];
        $hour = (int) $hour;
        $minute = (int) $minute;

        if ($schedule === 'daily') {
            $next = $now->copy()->setTime($hour, $minute, 0);
            if ($next->lessThanOrEqualTo($now)) {
                $next->addDay();
            }
            return $next;
        }

        if ($schedule === 'weekly') {
            $next = $now->copy()->next(Carbon::SUNDAY)->setTime($hour, $minute, 0);
            if ($next->lessThanOrEqualTo($now)) {
                $next->addWeek();
            }
            return $next;
        }

        if ($schedule === 'monthly') {
            $next = $now->copy()->firstOfMonth()->setTime($hour, $minute, 0);
            if ($next->lessThanOrEqualTo($now)) {
                $next->addMonth()->firstOfMonth();
            }
            return $next;
        }

        return null;
    }

    private function writeAudit(Request $request, string $entityType, string $action, array $details = [])
    {
        try {
            $user = $request->user();

            DB::table('asset_audit_logs')->insert([
                'EntityType' => $entityType,
                'EntityID' => 0,
                'Action' => $action,
                'PerformedBy' => $user->UserID ?? null,
                'Details' => json_encode($details),
                'IPAddress' => $request->ip(),
                'UserAgent' => $request->header('User-Agent'),
                'CreatedAt' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            // Don't break primary action if audit fails
            \Log::error('Failed to write backup audit: ' . $e->getMessage());
        }
    }
}
