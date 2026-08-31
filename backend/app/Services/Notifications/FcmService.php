<?php

namespace App\Services\Notifications;

use App\Models\Notification\UserPushToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public function __construct(private readonly FirebaseAccessTokenService $accessTokenService)
    {
    }

    /**
     * @param array<int> $userIds
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        if (!$this->isEnabled()) {
            return [
                'enabled' => false,
                'sent' => 0,
                'failed' => 0,
                'by_user' => [],
            ];
        }

        $normalizedUserIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($normalizedUserIds->isEmpty()) {
            return [
                'enabled' => true,
                'sent' => 0,
                'failed' => 0,
                'by_user' => [],
            ];
        }

        $tokens = UserPushToken::query()
            ->whereIn('UserID', $normalizedUserIds->all())
            ->where('is_active', true)
            ->orderByDesc('last_used_at')
            ->orderByDesc('id')
            ->get(['id', 'UserID', 'token'])
            ->unique('token')
            ->groupBy('UserID')
            ->flatMap(fn ($userTokens) => $userTokens->take(3))
            ->values();

        $resultsByUser = [];
        foreach ($normalizedUserIds as $userId) {
            $resultsByUser[$userId] = [
                'sent' => 0,
                'failed' => 0,
                'errors' => [],
            ];
        }

        if ($tokens->isEmpty()) {
            foreach ($resultsByUser as &$entry) {
                $entry['failed'] = 1;
                $entry['errors'][] = 'No active push token';
            }

            return [
                'enabled' => true,
                'sent' => 0,
                'failed' => count($resultsByUser),
                'by_user' => $resultsByUser,
            ];
        }

        $projectId = (string) config('services.firebase.project_id');
        $accessToken = $this->accessTokenService->getAccessToken();

        foreach ($tokens as $tokenRecord) {
            $payload = [
                'message' => [
                    'token' => $tokenRecord->token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => (string) ($data['link'] ?? '/'),
                        ],
                        'notification' => [
                            'icon' => '/assets/images/logo.png',
                        ],
                    ],
                    'data' => $this->normalizeDataForFcm($data),
                ],
            ];

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

            if ($response->successful()) {
                $resultsByUser[$tokenRecord->UserID]['sent']++;
                continue;
            }

            $resultsByUser[$tokenRecord->UserID]['failed']++;
            $errorBody = $response->json();
            $errorCode = (string) data_get($errorBody, 'error.status', 'UNKNOWN');
            $resultsByUser[$tokenRecord->UserID]['errors'][] = $errorCode;

            if (in_array($errorCode, ['NOT_FOUND', 'UNREGISTERED', 'INVALID_ARGUMENT'], true)) {
                UserPushToken::where('id', $tokenRecord->id)->update(['is_active' => false]);
            }

            Log::warning('FCM send failed', [
                'user_id' => $tokenRecord->UserID,
                'token_id' => $tokenRecord->id,
                'status' => $response->status(),
                'error' => $errorBody,
            ]);
        }

        $sent = 0;
        $failed = 0;
        foreach ($resultsByUser as $entry) {
            $sent += (int) $entry['sent'];
            $failed += (int) $entry['failed'];
        }

        return [
            'enabled' => true,
            'sent' => $sent,
            'failed' => $failed,
            'by_user' => $resultsByUser,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function normalizeDataForFcm(array $data): array
    {
        $normalized = [];
        foreach ($data as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }

            if (is_scalar($value) || $value === null) {
                $normalized[$key] = (string) $value;
                continue;
            }

            $normalized[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        return $normalized;
    }

    private function isEnabled(): bool
    {
        return filter_var((string) env('FCM_ENABLED', false), FILTER_VALIDATE_BOOLEAN)
            && (string) config('services.firebase.project_id') !== '';
    }
}
