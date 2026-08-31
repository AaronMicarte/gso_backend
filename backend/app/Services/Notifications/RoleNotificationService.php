<?php

namespace App\Services\Notifications;

use App\Models\Auth\User;
use App\Models\Notification\UserNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class RoleNotificationService
{
    public function __construct(private readonly FcmService $fcmService)
    {
    }

    /**
     * Determines whether an event is important enough for FCM Push Alerts to devices.
     * High-importance workflow transitions trigger push notifications.
     * Minor updates, edits, and status tweaks remain in-app database notifications only.
     */
    public function isPushEligible(string $eventType, bool $sendPush = true): bool
    {
        if (!$sendPush) {
            return false;
        }

        $inAppOnlyEvents = [
            'delivery_receipt.updated',
            'iar.updated',
            'inspection_report.updated',
            'asset_service_record.updated',
            'property_transfer.updated',
            'property_borrow.updated',
            'asset_request.updated',
        ];

        if (in_array($eventType, $inAppOnlyEvents, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string> $roles
     * @param array<string, mixed> $payload
     */
    public function notifyRoles(
        array $roles,
        string $eventType,
        string $title,
        string $message,
        array $payload = [],
        ?int $excludeUserId = null,
        bool $sendPush = true
    ): void {
        $roleNames = collect($roles)
            ->map(fn ($role) => trim((string) $role))
            ->filter()
            ->unique()
            ->values();

        if ($roleNames->isEmpty()) {
            return;
        }

        $query = User::query()
            ->select('UserID')
            ->where('IsActive', true)
            ->whereHas('userRole', function ($q) use ($roleNames) {
                $q->whereIn('RoleName', $roleNames->all());
            });

        if (!empty($excludeUserId)) {
            $query->where('UserID', '!=', $excludeUserId);
        }

        $userIds = $query->pluck('UserID')->map(fn ($id) => (int) $id)->all();
        $this->notifyUsers($userIds, $eventType, $title, $message, $payload, $sendPush);
    }

    /**
     * @param array<int> $userIds
     * @param array<string, mixed> $payload
     */
    public function notifyUsers(
        array $userIds,
        string $eventType,
        string $title,
        string $message,
        array $payload = [],
        bool $sendPush = true
    ): void {
        $normalizedUserIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($normalizedUserIds->isEmpty()) {
            return;
        }

        $isPushAllowed = $this->isPushEligible($eventType, $sendPush);

        foreach ($normalizedUserIds as $userId) {
            $payloadJson = empty($payload)
                ? null
                : json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            // Check for identical recent notification within last 15 seconds to prevent spam/duplicates
            $recentDuplicate = UserNotification::query()
                ->where('UserID', $userId)
                ->where('event_type', $eventType)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->first();

            if ($recentDuplicate && ($recentDuplicate->message === $message || $recentDuplicate->payload === $payloadJson)) {
                continue;
            }

            try {
                $notification = UserNotification::create([
                    'UserID' => $userId,
                    'event_type' => $eventType,
                    'title' => $title,
                    'message' => $message,
                    'payload' => $payloadJson,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (Throwable $e) {
                Log::error('Failed to insert user notification', [
                    'event_type' => $eventType,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }

            if (!$isPushAllowed) {
                // In-app only; skip FCM dispatch
                $notification->status = 'sent';
                $notification->sent_at = now();
                $notification->save();
                continue;
            }

            $userPayload = $payload;
            $userPayload['notification_id'] = (string) $notification->UserNotificationID;

            try {
                $fcmResult = $this->fcmService->sendToUsers([$userId], $title, $message, $userPayload);
            } catch (Throwable $e) {
                Log::error('FCM dispatch failed unexpectedly', [
                    'event_type' => $eventType,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);

                $fcmResult = [
                    'by_user' => [],
                ];
            }

            $entry = $fcmResult['by_user'][$userId] ?? null;
            $wasSent = is_array($entry) && ((int) ($entry['sent'] ?? 0) > 0);
            $errors = is_array($entry) ? ($entry['errors'] ?? []) : [];

            try {
                $notification->status = $wasSent ? 'sent' : 'failed';
                $notification->sent_at = $wasSent ? now() : null;
                $notification->error_message = $wasSent
                    ? null
                    : (empty($errors) ? 'No active push token or FCM disabled' : implode(', ', $errors));
                $notification->updated_at = now();
                $notification->save();
            } catch (Throwable $e) {
                Log::error('Failed to update notification status', [
                    'event_type' => $eventType,
                    'user_id' => $userId,
                    'notification_id' => $notification->UserNotificationID,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function notifyUser(
        int $userId,
        string $eventType,
        string $title,
        string $message,
        array $payload = [],
        bool $sendPush = true
    ): void {
        if ($userId <= 0) {
            return;
        }

        $this->notifyUsers([$userId], $eventType, $title, $message, $payload, $sendPush);
    }
}
