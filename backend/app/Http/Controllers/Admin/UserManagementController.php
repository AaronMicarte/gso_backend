<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountActivationMail;
use App\Models\Auth\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    private function assertAdmin(Request $request)
    {
        $user = $request->user();
        $roleName = $user?->userRole?->RoleName ?? '';
        if ($roleName !== 'Admin') {
            abort(403, 'Forbidden');
        }
    }

    public function index(Request $request)
    {
        $this->assertAdmin($request);

        $search = trim((string) $request->query('search', ''));

        $query = User::query()
            ->with(['department', 'userRole'])
            ->orderBy('UserID', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('Username', 'like', "%{$search}%")
                    ->orWhere('Email', 'like', "%{$search}%")
                    ->orWhere('FullName', 'like', "%{$search}%");
            });
        }

        $users = $query->get()->map(function (User $u) {
            return [
                'UserID' => $u->UserID,
                'Username' => $u->Username,
                'Email' => $u->Email,
                'FullName' => $u->FullName,
                'IsActive' => (bool) $u->IsActive,
                'EmailVerifiedAt' => $u->EmailVerifiedAt,
                'MFAEnabled' => (bool) $u->MFAEnabled,
                'UserRoleID' => $u->UserRoleID,
                'Role' => $u->userRole?->RoleName,
                'DepartmentID' => $u->DepartmentID,
                'Department' => $u->department?->DepartmentName,
                'CreatedDate' => $u->CreatedDate,
                'UpdatedDate' => $u->UpdatedDate,
            ];
        });

        return response()->json([
            'data' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $this->assertAdmin($request);

        $validated = $request->validate([
            'Username' => 'required|string|min:3|max:50',
            'Email' => 'required|email|max:100',
            'FullName' => 'nullable|string|max:100',
            'UserRoleID' => 'required|integer',
            'DepartmentID' => 'required|integer',
        ]);

        // Basic uniqueness checks (table has no unique indexes today)
        if (User::where('Username', $validated['Username'])->exists()) {
            return response()->json(['message' => 'Username already exists'], 422);
        }
        if (User::where('Email', $validated['Email'])->exists()) {
            return response()->json(['message' => 'Email already exists'], 422);
        }

        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = Carbon::now()->addHours(48);

        $user = new User();
        $user->UserRoleID = $validated['UserRoleID'];
        $user->DepartmentID = $validated['DepartmentID'];
        $user->Username = $validated['Username'];
        $user->Email = $validated['Email'];
        $user->FullName = $validated['FullName'] ?? null;

        // Per requirement: default temp password is "123" (will be forced to change during activation)
        $user->Password = Hash::make('123');

        $user->IsActive = 1;
        $user->MFAEnabled = 0;

        $user->EmailVerifiedAt = null;
        $user->ActivationTokenHash = $tokenHash;
        $user->ActivationSentAt = Carbon::now();
        $user->ActivationExpiresAt = $expiresAt;
        $user->MustChangePassword = 1;

        $user->CreatedDate = Carbon::now();
        $user->save();

        $mailResult = $this->sendActivationEmail($user, $plainToken);

        return response()->json([
            'message' => $mailResult['email_sent'] ? 'User created and activation email sent' : 'User created (activation email not sent)',
            'email_sent' => $mailResult['email_sent'],
            'activation_link' => $mailResult['activation_link'],
            'user' => [
                'UserID' => $user->UserID,
                'Username' => $user->Username,
                'Email' => $user->Email,
                'FullName' => $user->FullName,
                'IsActive' => (bool) $user->IsActive,
                'EmailVerifiedAt' => $user->EmailVerifiedAt,
                'MFAEnabled' => (bool) $user->MFAEnabled,
                'UserRoleID' => $user->UserRoleID,
                'DepartmentID' => $user->DepartmentID,
            ],
        ], 201);
    }

    public function toggleActive(Request $request, int $id)
    {
        $this->assertAdmin($request);

        $validated = $request->validate([
            'IsActive' => 'required|boolean',
        ]);

        $user = User::with(['userRole'])->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Prevent disabling yourself accidentally
        if ($request->user()?->UserID === $user->UserID) {
            return response()->json(['message' => 'You cannot change your own active status'], 422);
        }

        $user->IsActive = $validated['IsActive'] ? 1 : 0;
        $user->UpdatedDate = Carbon::now();
        $user->save();

        return response()->json([
            'message' => 'User updated',
            'user' => [
                'UserID' => $user->UserID,
                'IsActive' => (bool) $user->IsActive,
            ],
        ]);
    }

    public function resendActivation(Request $request, int $id)
    {
        $this->assertAdmin($request);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->EmailVerifiedAt) {
            return response()->json(['message' => 'User is already verified'], 422);
        }

        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = Carbon::now()->addHours(48);

        $user->ActivationTokenHash = $tokenHash;
        $user->ActivationSentAt = Carbon::now();
        $user->ActivationExpiresAt = $expiresAt;
        $user->UpdatedDate = Carbon::now();
        $user->save();

        $mailResult = $this->sendActivationEmail($user, $plainToken);

        return response()->json([
            'message' => $mailResult['email_sent'] ? 'Activation email sent' : 'Activation email not sent',
            'email_sent' => $mailResult['email_sent'],
            'activation_link' => $mailResult['activation_link'],
        ]);
    }

    private function sendActivationEmail(User $user, string $plainToken): array
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', 'http://localhost:3000'), '/');
        $activationLink = $frontendUrl . '/activate-account?token=' . urlencode($plainToken);

        $mailer = (string) config('mail.default', '');
        // In local/dev environments, Laravel often uses 'log' or 'array' mailers.
        // Those do NOT deliver email to an inbox, so we should not claim "email_sent".
        if (in_array($mailer, ['log', 'array', 'null'], true)) {
            \Log::warning('Activation email not delivered (non-delivery mailer)', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'mailer' => $mailer,
                'activation_link' => $activationLink,
            ]);

            return [
                'email_sent' => false,
                'activation_link' => $activationLink,
            ];
        }

        try {
            $mail = new AccountActivationMail(
                $user->FullName ?: $user->Username,
                $user->Username,
                $activationLink,
                $user->ActivationExpiresAt
            );

            Mail::to($user->Email)->send($mail);

            // `failures()` exists for some legacy mail drivers; guard it so we don't throw.
            $mailerInstance = Mail::getFacadeRoot();
            if ($mailerInstance && method_exists($mailerInstance, 'failures')) {
                $failures = Mail::failures();
                if (!empty($failures)) {
                    \Log::error('Activation email failed for recipients', [
                        'user_id' => $user->UserID,
                        'email' => $user->Email,
                        'failures' => $failures,
                    ]);

                    return [
                        'email_sent' => false,
                        'activation_link' => $activationLink,
                    ];
                }
            }

            return [
                'email_sent' => true,
                'activation_link' => $activationLink,
            ];
        } catch (\Exception $e) {
            \Log::error('Activation email send threw exception', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'mailer' => config('mail.default'),
                'error' => $e->getMessage(),
            ]);

            // Don't fail user creation if mail isn't configured; return link for manual sending.
            return [
                'email_sent' => false,
                'activation_link' => $activationLink,
            ];
        }
    }
}
