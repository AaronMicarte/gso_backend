<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Auth\User;
use App\Models\Auth\LoginAudit;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Max login attempts before lockout
    private const MAX_ATTEMPTS = 5;
    // Lockout duration in seconds (use 15 * 60 = 900 for production)
    private const LOCKOUT_SECONDS = 60; // 60 seconds for better testing

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
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // 2. Find User
        // We rate limit based on the user ID to protect specific accounts.
        $user = User::where('Username', $request->username)->first();

        // If user not found, return generic error (security best practice to avoid enumeration)
        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
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
                'Status' => 'FailedLogin'
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

        // Log successful login
        LoginAudit::create([
            'UserID' => $user->UserID,
            'EventTime' => Carbon::now(),
            'Status' => 'Login'
        ]);

        // Use Laravel's session-based auth (creates httpOnly cookie automatically)
        Auth::login($user);

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'UserID' => $user->UserID,
                'Username' => $user->Username,
                'Email' => $user->Email,
                'FullName' => $user->FullName ?? $user->Username,
                'Role' => $user->userRole->RoleName ?? 'N/A',
                'Department' => $user->department->DepartmentName ?? 'N/A',
            ]
        ]);
    }
    /**
     * Get authenticated user data
     */
    public function getUser(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'user' => [
                    'UserID' => $user->UserID,
                    'Username' => $user->Username,
                    'Email' => $user->Email,
                    'FullName' => $user->FullName ?? $user->Username,
                    'Role' => $user->userRole->RoleName ?? 'N/A',
                    'Department' => $user->department->DepartmentName ?? 'N/A',
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

            // Log logout event
            if ($request->user()) {
                LoginAudit::create([
                    'UserID' => $request->user()->UserID,
                    'EventTime' => Carbon::now(),
                    'Status' => 'Logout'
                ]);
            }
            return response()->json([
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
