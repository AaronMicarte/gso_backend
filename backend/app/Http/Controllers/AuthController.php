<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User;
use App\Models\Auth\LoginAudit;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
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

        $user = User::where('Username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->Password)) {
            // Log failed login attempt
            if ($user) {
                LoginAudit::create([
                    'UserID' => $user->UserID,
                    'Status' => 'Failed'
                ]);
            }

            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }

        try {
            // Log successful login
            LoginAudit::create([
                'UserID' => $user->UserID,
                'Status' => 'Success'
            ]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'UserID' => $user->UserID,
                    'Username' => $user->Username,
                    'Email' => $user->Email,
                    'Role' => $user->userRole->Role ?? 'N/A',
                    'Department' => $user->department->DepartmentName ?? 'N/A',
                ],
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
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
                    'Role' => $user->userRole->Role ?? 'N/A',
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
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

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
