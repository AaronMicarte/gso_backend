<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Auth\User;
use App\Models\Auth\LoginAudit;
use App\Models\Auth\TrustedDevice;
use App\Services\MFAService;
use App\Services\TurnstileService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    // Max login attempts before lockout
    private const MAX_ATTEMPTS = 5;
    // Lockout duration in seconds (use 15 * 60 = 900 for production)
    private const LOCKOUT_SECONDS = 60; // 60 seconds for better testing

    protected MFAService $mfaService;
    protected TurnstileService $turnstileService;

    public function __construct(MFAService $mfaService, TurnstileService $turnstileService)
    {
        $this->mfaService = $mfaService;
        $this->turnstileService = $turnstileService;
    }

    /**
     * Login user using session-based authentication (httpOnly cookie)
     * More secure than localStorage tokens - prevents XSS attacks
     */
    public function login(Request $request)
    {
        // 1. Input Validation
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
                'turnstile_token' => 'nullable|string',
                'device_token' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $turnstileCheck = $this->turnstileService->verifyToken($request->input('turnstile_token'), $request->ip());
        if (!$turnstileCheck['success']) {
            return response()->json([
                'message' => $turnstileCheck['message'] ?? 'Captcha verification failed.',
                'turnstile_failed' => true,
                'turnstile_errors' => $turnstileCheck['errors'] ?? [],
            ], 422);
        }

        // 2. Find User
        // We rate limit based on the user ID to protect specific accounts.
        $user = User::where('Username', $request->username)->first();

        // If user not found, return generic error (security best practice to avoid enumeration)
        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // 2.1 Account state checks
        if (!$user->IsActive) {
            return response()->json([
                'message' => 'Account is disabled. Please contact your GSO Admin.',
                'account_disabled' => true,
            ], 403);
        }

        if (!$user->EmailVerifiedAt) {
            return response()->json([
                'message' => 'Account activation required. Please check your email for the activation link.',
                'activation_required' => true,
            ], 403);
        }

        // 3. Check Rate Limiting
        // key: login_attempts:UserID
        $throttleKey = 'login_attempts:' . $user->UserID;

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => 'Too many failed attempts. Account locked.',
                'locked' => true,
                'seconds_remaining' => $seconds,
                'contact' => 'Please contact GSO Admin at gso@elsalvador.gov.ph'
            ], 429); // 429 Too Many Requests is standard for rate limiting
        }

        // 4. Verify Credentials
        if (!Hash::check($request->password, $user->Password)) {
            // Increment failed attempts
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            // Log the failed attempt
            LoginAudit::create([
                'UserID' => $user->UserID,
                'EventTime' => Carbon::now(),
                'Status' => 'FailedLogin',
                'IPAddress' => $request->ip(),
            ]);

            // Check if this failure triggered the lockout
            $attempts = RateLimiter::attempts($throttleKey);
            $remaining = self::MAX_ATTEMPTS - $attempts;

            if ($remaining <= 0) {
                return response()->json([
                    'message' => 'Too many failed attempts. Account locked.',
                    'locked' => true,
                    'seconds_remaining' => self::LOCKOUT_SECONDS,
                    'contact' => 'Please contact GSO Admin at gso@elsalvador.gov.ph'
                ], 429);
            }

            return response()->json([
                'message' => 'Invalid credentials',
                'attempts_remaining' => max(0, $remaining)
            ], 401);
        }

        // 5. Login Successful
        // Clear rate limiter on success
        RateLimiter::clear($throttleKey);

        if ((bool) $user->MustChangePassword) {
            return response()->json([
                'message' => 'Password change required before login.',
                'must_change_password' => true,
                'user_id' => $user->UserID,
                'username' => $user->Username,
            ], 200);
        }

        // Local/testing bypass: allow login without MFA code entry.
        // The environment variable `MFA_BYPASS_FOR_TESTING` can still be used locally
        // but defaults to false so MFA is enforced by default. Remove this block
        // altogether if you never want to bypass MFA.
        $bypassMfaForTesting = app()->environment('local')
            && filter_var((string) env('MFA_BYPASS_FOR_TESTING', 'false'), FILTER_VALIDATE_BOOLEAN);

        if ($bypassMfaForTesting) {
            LoginAudit::create([
                'UserID' => $user->UserID,
                'EventTime' => Carbon::now(),
                'Status' => 'Login',
                'IPAddress' => $request->ip(),
            ]);

            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->save();

            return response()->json([
                'message' => 'Login successful',
                'mfa_required' => false,
                'user' => [
                    'UserID' => $user->UserID,
                    'Username' => $user->Username,
                    'Email' => $user->Email,
                    'FullName' => $user->FullName ?? $user->Username,
                    'Role' => $user->userRole->RoleName ?? 'N/A',
                    'UserRoleID' => $user->UserRoleID,
                    'Department' => $user->department->DepartmentName ?? 'N/A',
                    'DepartmentID' => $user->DepartmentID,
                    'MFAEnabled' => (bool) $user->MFAEnabled,
                    'department' => [
                        'DepartmentID' => $user->department->DepartmentID ?? 0,
                        'DepartmentName' => $user->department->DepartmentName ?? 'N/A',
                    ],
                ],
            ]);
        }

        // 6. Check MFA Requirement
        // For testing/admin control, enforce MFA only when MFAEnabled is true on the user record.

        if ($user->MFAEnabled) {
            // Check if device is trusted
            $deviceToken = $request->cookie('device_token') ?: $request->input('device_token');

            if ($deviceToken) {
                $trustedDevice = $this->mfaService->verifyDeviceToken($user, $deviceToken);

                if ($trustedDevice) {
                    // Device is trusted, skip MFA
                    // Log successful login
                    LoginAudit::create([
                        'UserID' => $user->UserID,
                        'EventTime' => Carbon::now(),
                        'Status' => 'Login',
                        'IPAddress' => $request->ip(),
                    ]);

                    // Use Laravel's session-based auth
                    Auth::login($user);
                    $request->session()->regenerate();
                    
                    // Explicitly save session to prevent race condition
                    $request->session()->save();

                    $response = response()->json([
                        'message' => 'Login successful',
                        'mfa_required' => false,
                        'user' => [
                            'UserID' => $user->UserID,
                            'Username' => $user->Username,
                            'Email' => $user->Email,
                            'FullName' => $user->FullName ?? $user->Username,
                            'Role' => $user->userRole->RoleName ?? 'N/A',
                            'UserRoleID' => $user->UserRoleID,
                            'Department' => $user->department->DepartmentName ?? 'N/A',
                            'DepartmentID' => $user->DepartmentID,
                            'department' => [
                                'DepartmentID' => $user->department->DepartmentID ?? 0,
                                'DepartmentName' => $user->department->DepartmentName ?? 'N/A',
                            ]
                        ]
                    ]);

                    // Refresh device token as httpOnly cookie. This also repairs local dev
                    // sessions where the token was sent via payload fallback.
                    $secure = $request->isSecure() || app()->environment('production');
                    $sameSite = $secure ? 'None' : 'Lax';
                    $domain = null;
                    $response->cookie(
                        'device_token',
                        $deviceToken,
                        60 * 24 * 30,
                        '/',
                        $domain,
                        $secure,
                        true,
                        false,
                        $sameSite
                    );

                    return $response;
                }
            }

            // Device not trusted, require MFA verification
            return response()->json([
                'message' => 'MFA verification required',
                'mfa_required' => true,
                'mfa_setup_required' => false,
                'user_id' => $user->UserID,
                'user' => [
                    'UserID' => $user->UserID,
                    'Username' => $user->Username,
                    'Email' => $user->Email,
                    'FullName' => $user->FullName ?? $user->Username,
                    'MFAEnabled' => $user->MFAEnabled,
                ]
            ], 200);
        }

        // No MFA required when MFAEnabled is false
        // Log successful login
        LoginAudit::create([
            'UserID' => $user->UserID,
            'EventTime' => Carbon::now(),
            'Status' => 'Login',
            'IPAddress' => $request->ip(),
        ]);

        // Use Laravel's session-based auth (creates httpOnly cookie automatically)
        Auth::login($user);

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();
        
        // Explicitly save session to prevent race condition
        $request->session()->save();

        return response()->json([
            'message' => 'Login successful',
            'mfa_required' => false,
            'user' => [
                'UserID' => $user->UserID,
                'Username' => $user->Username,
                'Email' => $user->Email,
                'FullName' => $user->FullName ?? $user->Username,
                'Role' => $user->userRole->RoleName ?? 'N/A',
                'UserRoleID' => $user->UserRoleID,
                'Department' => $user->department->DepartmentName ?? 'N/A',
                'DepartmentID' => $user->DepartmentID,
                'MFAEnabled' => $user->MFAEnabled,
                'department' => [
                    'DepartmentID' => $user->department->DepartmentID ?? 0,
                    'DepartmentName' => $user->department->DepartmentName ?? 'N/A',
                ]
            ]
        ]);
    }

    /**
     * Mobile login endpoint for native apps that returns a Sanctum token.
     * This is separate from the session-based `login` method used by the SPA.
     */
    public function mobileLogin(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
                'device_token' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::where('Username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->IsActive) {
            return response()->json([
                'message' => 'Account is disabled. Please contact your GSO Admin.',
                'account_disabled' => true,
            ], 403);
        }

        if (!$user->EmailVerifiedAt) {
            return response()->json([
                'message' => 'Account activation required. Please check your email for the activation link.',
                'activation_required' => true,
            ], 403);
        }

        if (!Hash::check($request->password, $user->Password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create a personal access token for the mobile client
        $token = $user->createToken('mobile')->plainTextToken;

        // Optionally persist device token for push or trusted device flows
        if ($request->filled('device_token')) {
            try {
                $user->pushTokens()->create([
                    'Token' => $request->input('device_token'),
                    'Provider' => 'expo',
                ]);
            } catch (\Throwable $_) {
                // ignore push token persistence errors
            }
        }

        // Return token + user payload
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'mfa_required' => false,
            'user' => [
                'UserID' => $user->UserID,
                'Username' => $user->Username,
                'Email' => $user->Email,
                'FullName' => $user->FullName ?? $user->Username,
                'Role' => $user->userRole->RoleName ?? 'N/A',
                'UserRoleID' => $user->UserRoleID,
                'Department' => $user->department->DepartmentName ?? 'N/A',
                'DepartmentID' => $user->DepartmentID,
                'MFAEnabled' => (bool) $user->MFAEnabled,
                'department' => [
                    'DepartmentID' => $user->department->DepartmentID ?? 0,
                    'DepartmentName' => $user->department->DepartmentName ?? 'N/A',
                ],
            ],
        ]);
    }

    /**
     * Activate account using emailed token, verify email, and set a new password.
     * Public endpoint.
     */
    public function activateAccount(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string|min:20',
                'turnstile_token' => 'nullable|string',
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    PasswordRule::min(12)->mixedCase()->numbers()->symbols(),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $turnstileCheck = $this->turnstileService->verifyToken($request->input('turnstile_token'), $request->ip());
        if (!$turnstileCheck['success']) {
            return response()->json([
                'message' => $turnstileCheck['message'] ?? 'Captcha verification failed.',
                'turnstile_failed' => true,
                'turnstile_errors' => $turnstileCheck['errors'] ?? [],
            ], 422);
        }

        $tokenHash = hash('sha256', $request->token);

        $user = User::where('ActivationTokenHash', $tokenHash)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid or expired activation token'], 422);
        }

        if ($user->ActivationExpiresAt && Carbon::parse($user->ActivationExpiresAt)->isPast()) {
            return response()->json(['message' => 'Invalid or expired activation token'], 422);
        }

        if (!$user->IsActive) {
            return response()->json(['message' => 'Account is disabled. Please contact your GSO Admin.'], 403);
        }

        // Activate + verify
        $user->EmailVerifiedAt = Carbon::now();
        $user->ActivationTokenHash = null;
        $user->ActivationSentAt = null;
        $user->ActivationExpiresAt = null;
        $user->MustChangePassword = 0;

        // Set new password
        $user->Password = Hash::make($request->password);
        $user->UpdatedDate = Carbon::now();
        $user->save();

        // Determine whether this user should be prompted to set up MFA.  Some
        // roles are required to have MFA even if the user hasn't enabled it yet.
        $userRole = $user->userRole->RoleName ?? '';
        $mfaRequiredRoles = ['Admin', 'GSO Staff', 'Department Head', 'Inspection Officer'];
        $setupRequired = !$user->MFAEnabled && in_array($userRole, $mfaRequiredRoles);

        // Return info for client; MFA setup is optional unless explicitly enabled.
        return response()->json([
            'message' => 'Account activated successfully',
            'user_id' => $user->UserID,
            'mfa_setup_required' => $setupRequired,
            'mfa_enabled' => (bool) $user->MFAEnabled,
        ]);
    }

    /**
     * Change temporary/default password before completing login.
     */
    public function changeTemporaryPassword(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'confirmed',
                    PasswordRule::min(12)->mixedCase()->numbers()->symbols(),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::where('Username', $request->input('username'))->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        if (!$user->IsActive) {
            return response()->json([
                'message' => 'Account is disabled. Please contact your GSO Admin.',
                'account_disabled' => true,
            ], 403);
        }

        if (!$user->EmailVerifiedAt) {
            return response()->json([
                'message' => 'Account activation required. Please check your email for the activation link.',
                'activation_required' => true,
            ], 403);
        }

        if (!(bool) $user->MustChangePassword) {
            return response()->json(['message' => 'Password change is not required for this account.'], 422);
        }

        if (!Hash::check((string) $request->input('current_password'), (string) $user->Password)) {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        $user->Password = Hash::make((string) $request->input('new_password'));
        $user->MustChangePassword = 0;
        $user->UpdatedDate = Carbon::now();
        $user->save();

        $this->mfaService->revokeAllDevices($user);

        return response()->json([
            'message' => 'Password updated successfully. Please login with your new password.',
        ]);
    }

    /**
     * Complete MFA login after TOTP verification.
     */
    public function completeMFALogin(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'code' => 'required|string|min:6|max:8',
                'trust_device' => 'boolean',
                'device_name' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Decrypt MFA secret for verification
        try {
            $decryptedSecret = decrypt($user->MFASecret);
        } catch (\Exception $e) {
            return response()->json(['message' => 'MFA configuration error'], 500);
        }

        // Temporarily set decrypted secret for verification
        $originalSecret = $user->MFASecret;
        $user->MFASecret = $decryptedSecret;

        // Try TOTP code first
        try {
            $codeValid = $this->mfaService->verifyPlainCode($decryptedSecret, $request->code);
        } catch (\Exception $e) {
            $codeValid = false;
            \Log::error('MFA verification failed during setup', [
                'user_id' => $user->UserID,
                'error' => $e->getMessage(),
            ]);
        }

        // Restore encrypted secret
        $user->MFASecret = $originalSecret;

        if (!$codeValid) {
            // Try backup code
            try {
                if (!$this->mfaService->verifyBackupCode($user, $request->code)) {
                    return response()->json(['message' => 'Invalid MFA code'], 422);
                }
            } catch (\Exception $e) {
                \Log::error('Backup code verification failed during setup', [
                    'user_id' => $user->UserID,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['message' => 'Invalid MFA code'], 422);
            }
        }

        // Code verified, complete login
        LoginAudit::create([
            'UserID' => $user->UserID,
            'EventTime' => Carbon::now(),
            'Status' => 'Login',
            'IPAddress' => $request->ip(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        
        // Explicitly save session to prevent race condition
        $request->session()->save();

        // Trust device if requested
        $response = response()->json([
            'message' => 'Login successful',
            'user' => [
                'UserID' => $user->UserID,
                'Username' => $user->Username,
                'Email' => $user->Email,
                'FullName' => $user->FullName ?? $user->Username,
                'Role' => $user->userRole->RoleName ?? 'N/A',
                'UserRoleID' => $user->UserRoleID,
                'Department' => $user->department->DepartmentName ?? 'N/A',
                'DepartmentID' => $user->DepartmentID,
                'department' => [
                    'DepartmentID' => $user->department->DepartmentID ?? 0,
                    'DepartmentName' => $user->department->DepartmentName ?? 'N/A',
                ]
            ]
        ]);

        if ($request->trust_device) {
            $device = $this->mfaService->createTrustedDevice(
                $user,
                $request->device_name ?? $this->getDeviceName($request),
                $request->header('User-Agent'),
                $request->ip()
            );

            // Determine secure and sameSite based on environment and request
            // Match logic used in MFAController::verifyCode so behavior is consistent
            $secure = $request->isSecure() || app()->environment('production');
            $sameSite = $secure ? 'None' : 'Lax';

            // Set cookie (30 days)
            // Use host-only cookie in local development to avoid localhost/127.0.0.1 mismatch.
            $domain = null;
            $response->cookie(
                'device_token',
                $device->plain_token,
                60 * 24 * 30, // 30 days in minutes
                '/',
                $domain, // 'localhost' for local, null for production
                $secure,  // secure set based on environment/request
                true,  // httpOnly
                false, // raw
                $sameSite // sameSite
            );
        }

        return $response;
    }

    /**
     * Generate a device name from the request.
     */
    private function getDeviceName(Request $request): string
    {
        $userAgent = $request->header('User-Agent', 'Unknown Device');

        // Parse user agent for common browsers/OS
        if (str_contains($userAgent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            $browser = 'Edge';
        } else {
            $browser = 'Browser';
        }

        if (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            $os = 'Mac';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'iOS')) {
            $os = 'iOS';
        } else {
            $os = 'Device';
        }

        return "$browser on $os";
    }
    /**
     * Get authenticated user data
     */
    public function getUser(Request $request)
    {
        try {
            // Eager load department and userRole relationships
            $user = $request->user()->load(['department', 'userRole']);

            return response()->json([
                'user' => [
                    'UserID' => $user->UserID,
                    'Username' => $user->Username,
                    'Email' => $user->Email,
                    'FullName' => $user->FullName ?? $user->Username,
                    'Role' => $user->userRole->RoleName ?? 'N/A',
                    'UserRoleID' => $user->UserRoleID,
                    'Department' => $user->department->DepartmentName ?? 'N/A',
                    'DepartmentID' => $user->DepartmentID,
                    'MFAEnabled' => $user->MFAEnabled,
                    'department' => [
                        'DepartmentID' => $user->department->DepartmentID ?? 0,
                        'DepartmentName' => $user->department->DepartmentName ?? 'N/A',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch user data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user and invalidate session
     */
    public function logout(Request $request)
    {
        try {
            // Logout from session-based auth
            Auth::guard('web')->logout();

            // Invalidate session and regenerate CSRF token
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Clear device token cookie to require MFA verification on next login
            $response = response()->json([
                'message' => 'Logout successful'
            ])->cookie('device_token', '', -1, '/'); // Expire the cookie

            // Log logout event
            if ($request->user()) {
                LoginAudit::create([
                    'UserID' => $request->user()->UserID,
                    'EventTime' => Carbon::now(),
                    'Status' => 'Logout',
                    'IPAddress' => $request->ip(),
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate MFA secret during login flow (for roles that require MFA but haven't set it up).
     * This is a PUBLIC endpoint - validates user_id exists and role requires MFA.
     */
    public function generateMFASecretForLogin(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verify this user's role actually requires MFA
        $mfaRequiredRoles = ['Admin', 'GSO Staff', 'Department Head', 'Inspection Officer'];
        $userRole = $user->userRole->RoleName ?? '';

        if (!in_array($userRole, $mfaRequiredRoles)) {
            return response()->json(['message' => 'MFA setup not required for this role'], 403);
        }

        // Check if already enabled
        if ($user->MFAEnabled) {
            return response()->json(['message' => 'MFA is already enabled'], 400);
        }

        // Generate secret
        $secretData = $this->mfaService->generateSecret($user);

        return response()->json([
            'secret' => $secretData['secret'],
            'qr_code_url' => $secretData['qr_code_url'],
            'message' => 'Scan the QR code with your authenticator app',
        ]);
    }

    /**
     * Enable MFA during login flow (for roles that require MFA setup).
     * This is a PUBLIC endpoint - validates user and enables MFA.
     */
    public function enableMFADuringLogin(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'secret' => 'required|string',
                'code' => 'required|string|size:6',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verify role requires MFA
        $mfaRequiredRoles = ['Admin', 'GSO Staff', 'Department Head', 'Inspection Officer'];
        $userRole = $user->userRole->RoleName ?? '';

        if (!in_array($userRole, $mfaRequiredRoles)) {
            return response()->json(['message' => 'MFA setup not required for this role'], 403);
        }

        // Enable MFA
        if (!$this->mfaService->enableMFA($user, $request->secret, $request->code)) {
            return response()->json(['message' => 'Invalid verification code. Please try again.'], 422);
        }

        // Get backup codes
        $backupCodes = json_decode(decrypt($user->BackupCodes), true);

        return response()->json([
            'message' => 'MFA has been enabled successfully. Please verify to complete login.',
            'backup_codes' => $backupCodes,
            'mfa_enabled' => true,
        ]);
    }
}
