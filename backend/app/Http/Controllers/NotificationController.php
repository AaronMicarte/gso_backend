<?php

namespace App\Http\Controllers;

use App\Models\Notification\UserNotification;
use App\Models\Notification\UserPushToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function registerPushToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:512',
            'platform' => 'nullable|string|in:web,android,ios',
            'device_name' => 'nullable|string|max:120',
        ]);

        $user = Auth::user();

        UserPushToken::updateOrCreate(
            ['token' => $validated['token']],
            [
                'UserID' => (int) $user->UserID,
                'platform' => $validated['platform'] ?? 'web',
                'device_name' => $validated['device_name'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json(['message' => 'Push token registered']);
    }

    public function removePushToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:512',
        ]);

        $user = Auth::user();

        UserPushToken::query()
            ->where('UserID', (int) $user->UserID)
            ->where('token', $validated['token'])
            ->update([
                'is_active' => false,
                'last_used_at' => now(),
            ]);

        return response()->json(['message' => 'Push token removed']);
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = min(100, max(10, (int) $request->query('per_page', 20)));

        $notifications = UserNotification::query()
            ->where('UserID', (int) $user->UserID)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($notifications);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $user = Auth::user();

        $notification = UserNotification::query()
            ->where('UserNotificationID', $id)
            ->where('UserID', (int) $user->UserID)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->read_at = now();
            $notification->save();
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();

        UserNotification::query()
            ->where('UserID', (int) $user->UserID)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();

        $deleted = UserNotification::query()
            ->where('UserNotificationID', $id)
            ->where('UserID', (int) $user->UserID)
            ->delete();

        if ($deleted < 1) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(['message' => 'Notification deleted']);
    }

    public function destroyAll(): JsonResponse
    {
        $user = Auth::user();

        $deletedCount = UserNotification::query()
            ->where('UserID', (int) $user->UserID)
            ->delete();

        return response()->json([
            'message' => 'All notifications deleted',
            'deleted_count' => (int) $deletedCount,
        ]);
    }
}
