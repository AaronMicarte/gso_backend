<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\LoginAudit;
use App\Models\Auth\TrustedDevice;
use App\Services\MFAService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MFAController extends Controller
{
    protected MFAService $mfaService;

    public function __construct(MFAService $mfaService)
    {
        $this->mfaService = $mfaService;
    }

    /**
     * Generate TOTP secret and QR code for MFA setup.
     */
    public function generateSecret(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $secretData = $this->mfaService->generateSecret($user);

        return response()->json([
            'secret' => $secretData['secret'],
            'qr_code_url' => $secretData['qr_code_url'],
            'message' => 'Scan the QR code with Google Authenticator or similar app',
        ]);
    }

    /**
     * Verify and enable TOTP MFA for the user.
     */
    public function enableMFA(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if (!$this->mfaService->enableMFA($user, $validated['secret'], $validated['code'])) {
            return response()->json(
                ['error' => 'Invalid verification code. Please try again.'],
                422
            );
        }

        $backupCodes = json_decode(decrypt($user->BackupCodes), true);

        return response()->json([
            'message' => 'MFA has been successfully enabled',
            'backup_codes' => $backupCodes,
            'warning' => 'Save these backup codes in a secure location. They can be used if you lose access to your authenticator app.',
        ]);
    }

    /**
     * Disable MFA for the user (requires password confirmation).
     */
    public function disableMFA(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        // Verify password
        if (!Hash::check($validated['password'], $user->Password)) {
            return response()->json(['error' => 'Invalid password'], 422);
        }

        // Verify MFA code
        try {
            if (!$this->mfaService->verifyCode($user, $validated['code'])) {
                return response()->json(['error' => 'Invalid MFA code'], 422);
            }
        } catch (\Exception $e) {
            \Log::error('MFA verification failed during disable', [
                'user_id' => $user->UserID,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid MFA code'], 422);
        }

        // Disable MFA and revoke all trusted devices
        $this->mfaService->disableMFA($user);
        $this->mfaService->revokeAllDevices($user);

        return response()->json(['message' => 'MFA has been disabled']);
    }

    /**
     * Verify TOTP code during login.
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            // Accept either a 6-digit TOTP code or an 8-character backup code
            'code' => 'required|string|min:6|max:8|regex:/^[A-Za-z0-9]+$/',
            'trust_device' => 'boolean',
            'device_name' => 'nullable|string|max:255',
        ]);

        // Normalize code (defensive: strip spaces and uppercase)
        $validated['code'] = strtoupper(preg_replace('/\s+/', '', $validated['code']));

        $user = User::find($validated['user_id']);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Try TOTP code first
        try {
            if ($this->mfaService->verifyCode($user, $validated['code'])) {
                // Code verified - establish session
                \Log::info('MFA code verified, creating login audit', ['user_id' => $user->UserID]);
                LoginAudit::create([
                    'UserID' => $user->UserID,
                    'EventTime' => Carbon::now(),
                    'Status' => 'Login',
                    'IPAddress' => $request->ip(),
                ]);
                Auth::login($user);
                $request->session()->regenerate();
                $request->session()->save();

                if ($validated['trust_device'] ?? false) {
                    $device = $this->mfaService->createTrustedDevice(
                        $user,
                        $validated['device_name'] ?? 'Trusted Device',
                        $request->header('User-Agent'),
                        $request->ip()
                    );

                    // Return device token to set as cookie
                    // Use secure cookie settings in production or when request is over HTTPS.
                    // For local development over HTTP, send a cookie usable in same-site contexts.
                    $secure = $request->isSecure() || app()->environment('production');
                    $sameSite = $secure ? 'None' : 'Lax';
                    $domain = null; // Don't set domain for local development to avoid cross-origin issues

                    return response()->json([
                        'message' => 'MFA code verified',
                        'device_token' => $device->plain_token,
                        'user' => $user->only(['UserID', 'Username', 'Email', 'FullName', 'Role', 'UserRoleID', 'DepartmentID']),
                        'user_role' => $user->userRole->only(['RoleName']) ?? null,
                        'department' => $user->department->only(['DepartmentID', 'DepartmentName']) ?? null,
                    ])->cookie('device_token', $device->plain_token, 60 * 24 * 30, '/', $domain, $secure, true, false, $sameSite);
                }

                return response()->json([
                    'message' => 'MFA code verified',
                    'user' => $user->only(['UserID', 'Username', 'Email', 'FullName', 'Role', 'UserRoleID', 'DepartmentID']),
                    'user_role' => $user->userRole->only(['RoleName']) ?? null,
                    'department' => $user->department->only(['DepartmentID', 'DepartmentName']) ?? null,
                ]);
            }
        } catch (\Exception $e) {
            // Log the error and continue to backup code check
            \Log::error('MFA verification failed', [
                'user_id' => $user->UserID,
                'error' => $e->getMessage(),
            ]);
        }

        // Try backup code
        try {
            if ($this->mfaService->verifyBackupCode($user, $validated['code'])) {
                // Backup code verified - establish session
                \Log::info('MFA backup code verified, creating login audit', ['user_id' => $user->UserID]);
                LoginAudit::create([
                    'UserID' => $user->UserID,
                    'EventTime' => Carbon::now(),
                    'Status' => 'Login',
                    'IPAddress' => $request->ip(),
                ]);
                Auth::login($user);
                $request->session()->regenerate();
                $request->session()->save();

                if ($validated['trust_device'] ?? false) {
                    $device = $this->mfaService->createTrustedDevice(
                        $user,
                        $validated['device_name'] ?? 'Trusted Device',
                        $request->header('User-Agent'),
                        $request->ip()
                    );

                    // Return device token to set as cookie
                    // Use secure cookie settings in production or when request is over HTTPS.
                    // For local development over HTTP, send a cookie usable in same-site contexts.
                    $secure = $request->isSecure() || app()->environment('production');
                    $sameSite = $secure ? 'None' : 'Lax';
                    $domain = null; // Don't set domain for local development to avoid cross-origin issues

                    return response()->json([
                        'message' => 'Backup code verified',
                        'device_token' => $device->plain_token,
                        'user' => $user->only(['UserID', 'Username', 'Email', 'FullName', 'Role', 'UserRoleID', 'DepartmentID']),
                        'user_role' => $user->userRole->only(['RoleName']) ?? null,
                        'department' => $user->department->only(['DepartmentID', 'DepartmentName']) ?? null,
                        'warning' => 'You used a backup code. Please add new backup codes in your security settings.',
                    ])->cookie('device_token', $device->plain_token, 60 * 24 * 30, '/', $domain, $secure, true, false, $sameSite);
                }

                return response()->json([
                    'message' => 'Backup code verified',
                    'user' => $user->only(['UserID', 'Username', 'Email', 'FullName', 'Role', 'UserRoleID', 'DepartmentID']),
                    'user_role' => $user->userRole->only(['RoleName']) ?? null,
                    'department' => $user->department->only(['DepartmentID', 'DepartmentName']) ?? null,
                    'warning' => 'You used a backup code. Please add new backup codes in your security settings.',
                ]);
            }
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Backup code verification failed', [
                'user_id' => $user->UserID,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['error' => 'Invalid code or backup code'], 422);
    }

    /**
     * Get list of trusted devices for the user.
     */
    public function getTrustedDevices(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $devices = $this->mfaService->getValidDevices($user)->map(function ($device) {
            return [
                'device_id' => $device->DeviceID,
                'device_name' => $device->DeviceName,
                'ip_address' => $device->IPAddress,
                'created_at' => $device->CreatedDate->format('Y-m-d H:i:s'),
                'expires_at' => $device->ExpiresAt->format('Y-m-d H:i:s'),
                'last_used_at' => $device->LastUsedAt ? $device->LastUsedAt->format('Y-m-d H:i:s') : null,
            ];
        });

        return response()->json(['devices' => $devices]);
    }

    /**
     * Revoke a specific trusted device.
    /**
     * Revoke a specific trusted device.
     */
    public function revokeTrustedDevice(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'device_id' => 'required|integer',
        ]);

        $device = TrustedDevice::where('UserID', $user->UserID)
            ->where('DeviceID', $validated['device_id'])
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $this->mfaService->revokeDevice($device);

        return response()->json(['message' => 'Device has been revoked']);
    }

    /**
     * Get MFA status for the user.
     */
    public function getMFAStatus(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'mfa_enabled' => (bool) $user->MFAEnabled,
            'has_backup_codes' => (bool) $user->BackupCodes,
            'trusted_devices_count' => $this->mfaService->getValidDevices($user)->count(),
        ]);
    }
}
