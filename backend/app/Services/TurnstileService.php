<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * Verify Cloudflare Turnstile token against the official siteverify API.
     *
     * @return array{success: bool, message?: string, errors?: array<int, string>}
     */
    public function verifyToken(?string $token, ?string $remoteIp = null): array
    {
        if (!$this->isEnabled()) {
            return ['success' => true];
        }

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Captcha verification is required.',
                'errors' => ['missing-input-response'],
            ];
        }

        $secretKey = (string) config('services.turnstile.secret_key', '');
        if ($secretKey === '') {
            return [
                'success' => false,
                'message' => 'Captcha service is not configured on the server.',
                'errors' => ['missing-input-secret'],
            ];
        }

        try {
            $verifyUrl = (string) config('services.turnstile.verify_url', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');

            $response = Http::asForm()
                ->timeout(10)
                ->post($verifyUrl, [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]);

            if (!$response->ok()) {
                return [
                    'success' => false,
                    'message' => 'Captcha verification request failed.',
                    'errors' => ['bad-request'],
                ];
            }

            $payload = $response->json();
            $success = (bool) ($payload['success'] ?? false);
            $errors = array_values((array) ($payload['error-codes'] ?? []));

            if (!$success) {
                return [
                    'success' => false,
                    'message' => 'Captcha verification failed. Please try again.',
                    'errors' => $errors,
                ];
            }

            $expectedHostname = (string) config('services.turnstile.expected_hostname', '');
            $actualHostname = (string) ($payload['hostname'] ?? '');

            if ($expectedHostname !== '' && $actualHostname !== '' && strcasecmp($expectedHostname, $actualHostname) !== 0) {
                return [
                    'success' => false,
                    'message' => 'Captcha verification failed due to hostname mismatch.',
                    'errors' => ['hostname-mismatch'],
                ];
            }

            return ['success' => true];
        } catch (\Throwable $e) {
            report($e);

            return [
                'success' => false,
                'message' => 'Captcha verification is temporarily unavailable.',
                'errors' => ['verification-exception'],
            ];
        }
    }

    private function isEnabled(): bool
    {
        return (bool) config('services.turnstile.enabled', false);
    }
}
