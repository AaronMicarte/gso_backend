<?php

namespace App\Console\Commands;

use App\Models\Auth\TrustedDevice;
use App\Models\Auth\User;
use Illuminate\Console\Command;

class ResetMfaCommand extends Command
{
    protected $signature = 'gso:mfa-reset
                            {user_id? : The UserID to reset MFA for}
                            {--all : Reset MFA for all users}
                            {--force : Do not prompt for confirmation}';

    protected $description = 'Reset MFA secrets/backup codes and trusted devices (useful after APP_KEY rotation)';

    public function handle(): int
    {
        $resetAll = (bool) $this->option('all');
        $force = (bool) $this->option('force');
        $userId = $this->argument('user_id');

        if (!$resetAll && $userId === null) {
            $this->error('Provide {user_id} or use --all.');
            return 1;
        }

        $query = User::query();

        if ($resetAll) {
            if (!$force) {
                $confirmed = $this->confirm('This will DISABLE MFA and DELETE trusted devices for ALL users. Continue?', false);
                if (!$confirmed) {
                    $this->info('Cancelled.');
                    return 0;
                }
            }
        } else {
            $query->where('UserID', (int) $userId);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn('No matching users found.');
            return 0;
        }

        $updatedUsers = 0;
        $deletedDevices = 0;

        foreach ($users as $user) {
            $updated = $user->forceFill([
                'MFAEnabled' => 0,
                'MFASecret' => null,
                'BackupCodes' => null,
            ])->save();

            if ($updated) {
                $updatedUsers++;
            }

            $deletedDevices += TrustedDevice::where('UserID', $user->UserID)->delete();
        }

        $this->info("MFA reset complete. Users updated: {$updatedUsers}. Trusted devices deleted: {$deletedDevices}.");
        $this->line('Next: users will need to set up MFA again.');

        return 0;
    }
}
