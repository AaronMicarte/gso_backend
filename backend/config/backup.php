<?php

$default = [
    // 'schedule' => 'daily' | 'weekly' | 'monthly' | 'disabled'
    'schedule' => env('DB_BACKUP_SCHEDULE', 'disabled'),

    // preferred backup time in 24-hour HH:MM format
    'time' => env('DB_BACKUP_TIME', '00:00'),

    // storage disk to store backups (must be configured in filesystems.php)
    'disk' => env('DB_BACKUP_DISK', 'backups'),

    // path inside disk
    'directory' => env('DB_BACKUP_DIRECTORY', 'backups'),

    // whether web restore is allowed (default false for safety)
    'allow_web_restore' => env('DB_BACKUP_ALLOW_WEB_RESTORE', false),

    // retention days
    'retention_days' => env('DB_BACKUP_RETENTION_DAYS', 30),
];

$settingsPath = storage_path('app/backup-settings.json');
if (file_exists($settingsPath)) {
    $stored = json_decode(file_get_contents($settingsPath), true);
    if (is_array($stored)) {
        $default = array_merge($default, $stored);
    }
}

$allowed = ['disabled', 'daily', 'weekly', 'monthly'];
if (!in_array($default['schedule'], $allowed, true)) {
    $default['schedule'] = 'disabled';
}

if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $default['time'])) {
    $default['time'] = '00:00';
}

return $default;
