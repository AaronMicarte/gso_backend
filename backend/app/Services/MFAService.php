<?php

namespace App\Services;

use App\Models\Auth\User;
use App\Models\Auth\TrustedDevice;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class MFAService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a new TOTP secret for a user.
     * Returns the secret and QR code URL (otpauth:// URI for QR code generation).
     */
    public function generateSecret(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();

        // Generate otpauth:// URL for QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'GSO IAMS'),
            $user->Email,
            $secret
        );

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ];
    }

    /**
     * Verify a TOTP code for a user.
     */
    public function verifyCode(User $user, string $code): bool
    {
        if (!$user->MFASecret) {
            return false;
        }

        return $this->google2fa->verifyKey($user->MFASecret, $code);
    }

    /**
     * Enable MFA for a user with verified code.
     */
    public function enableMFA(User $user, string $secret, string $code): bool
    {
        // Verify the code with the new secret
        if (!$this->google2fa->verifyKey($secret, $code)) {
            return false;
        }

        // Encrypt and store the secret
        $user->update([
            'MFASecret' => encrypt($secret),
            'MFAEnabled' => true,
        ]);

        // Generate and store backup codes
        $backupCodes = $this->generateBackupCodes();
        $user->update([
            'BackupCodes' => encrypt(json_encode($backupCodes)),
        ]);

        return true;
    }

    /**
     * Disable MFA for a user.
     */
    public function disableMFA(User $user): void
    {
        $user->update([
            'MFASecret' => null,
            'MFAEnabled' => false,
            'BackupCodes' => null,
        ]);
    }

    /**
     * Generate backup codes for account recovery.
     */
    public function generateBackupCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(8));
        }

        return $codes;
    }

    /**
     * Verify and consume a backup code.
     */
    public function verifyBackupCode(User $user, string $code): bool
    {
        if (!$user->BackupCodes) {
            return false;
        }

        $codes = json_decode(decrypt($user->BackupCodes), true);

        if (!in_array($code, $codes)) {
            return false;
        }

        // Remove the used code
        $codes = array_filter($codes, fn($c) => $c !== $code);
        $user->update(['BackupCodes' => encrypt(json_encode($codes))]);

        return true;
    }

    /**
     * Create a trusted device token for a user.
     */
    public function createTrustedDevice(
        User $user,
        string $deviceName = null,
        string $userAgent = null,
        string $ipAddress = null,
        int $expiryDays = 30
    ): TrustedDevice {
        // Generate a secure, random device token
        $token = bin2hex(random_bytes(32));

        $device = TrustedDevice::create([
            'UserID' => $user->UserID,
            'DeviceToken' => hash('sha256', $token), // Store hashed token
            'DeviceName' => $deviceName,
            'UserAgent' => $userAgent,
            'IPAddress' => $ipAddress,
            'CreatedDate' => now(),
            'ExpiresAt' => now()->addDays($expiryDays),
        ]);

        // Return device with plain token (only shown once)
        $device->plain_token = $token;

        // Dev-friendly log entry to help debug cookie creation/verification
        if (app()->environment('local') || config('app.debug')) {
            
            logger()->debug('trusted-device.created', [
                'user_id' => $user->UserID,
                'device_id' => $device->DeviceID,
                'device_name' => $deviceName,
                'ip' => $ipAddress,
                'user_agent' => substr($userAgent ?? 'n/a', 0, 200),
            ]);
        }

        return $device;
    }

    /**
     * Verify a device token for a user.
     */
    public function verifyDeviceToken(User $user, string $token): ?TrustedDevice
    {
        $hashedToken = hash('sha256', $token);

        $device = TrustedDevice::where('UserID', $user->UserID)
            ->where('DeviceToken', $hashedToken)
            ->first();

        if (!$device || !$device->isValid()) {
            if (app()->environment('local') || config('app.debug')) {
                logger()->debug('trusted-device.verify_failed', [
                    'user_id' => $user->UserID,
                    'token_hash' => substr($hashedToken, 0, 16),
                ]);
            }
            return null;
        }

        $device->updateLastUsed();

        if (app()->environment('local') || config('app.debug')) {
            logger()->debug('trusted-device.verified', [
                'user_id' => $user->UserID,
                'device_id' => $device->DeviceID,
            ]);
        }

        return $device;
    }

    /**
     * Revoke all trusted devices for a user (e.g., on password change).
     */
    public function revokeAllDevices(User $user): void
    {
        TrustedDevice::where('UserID', $user->UserID)->delete();
    }

    /**
     * Revoke a specific trusted device.
     */
    public function revokeDevice(TrustedDevice $device): void
    {
        $device->delete();
    }

    /**
     * Get all valid trusted devices for a user.
     */
    public function getValidDevices(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return TrustedDevice::where('UserID', $user->UserID)
            ->where('ExpiresAt', '>', now())
            ->orderBy('LastUsedAt', 'desc')
            ->get();
    }
}
