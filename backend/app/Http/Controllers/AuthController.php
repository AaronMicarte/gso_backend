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
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

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
                'message' => 'Invalid credentials'
            ], 401);
        }

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
                'Role' => $user->userRole->Role,
                'Department' => $user->department->DepartmentName,
            ],
            'token' => $token
        ]);
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
