<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * Verify Cloudflare Turnstile token against the official siteverify API.
     *
     * @param string|null $actionType Examples: login, mfa, activate-account
     * @param string|null $userAgent for enriched analytics
     * @return array{success: bool, message?: string, errors?: array<int, string>}
     */
    public function verifyToken(?string $token, ?string $remoteIp = null, string $actionType = 'unknown', ?string $userAgent = null): array
    {
        $auditPayload = [
            'ActionType' => $actionType,
            'Outcome' => 'failed',
            'IPAddress' => $remoteIp,
            'UserAgent' => $userAgent,
        ];

        if (!$this->isEnabled()) {
            $auditPayload['Outcome'] = 'passed';
            $this->audit($auditPayload);
            return ['success' => true];
        }

        if (!$token) {
            $auditPayload['ErrorCodes'] = json_encode(['missing-input-response']);
            $this->audit($auditPayload);

            return [
                'success' => false,
                'message' => 'Captcha verification is required.',
                'errors' => ['missing-input-response'],
            ];
        }

        $secretKey = (string) config('services.turnstile.secret_key', '');
        if ($secretKey === '') {
            $auditPayload['ErrorCodes'] = json_encode(['missing-input-secret']);
            $this->audit($auditPayload);

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
                $auditPayload['ErrorCodes'] = json_encode(['bad-request']);
                $this->audit($auditPayload);

                return [
                    'success' => false,
                    'message' => 'Captcha verification request failed.',
                    'errors' => ['bad-request'],
                ];
            }

            $payload = $response->json();
            $auditPayload['RawPayload'] = json_encode($payload);
            $auditPayload['Hostname'] = $payload['hostname'] ?? null;
            $auditPayload['Country'] = $payload['country'] ?? null;

            $success = (bool) ($payload['success'] ?? false);
            $errors = array_values((array) ($payload['error-codes'] ?? []));

            if (!$success) {
                $auditPayload['ErrorCodes'] = json_encode($errors);
                $this->audit($auditPayload);

                return [
                    'success' => false,
                    'message' => 'Captcha verification failed. Please try again.',
                    'errors' => $errors,
                ];
            }

            $expectedHostname = (string) config('services.turnstile.expected_hostname', '');
            $actualHostname = (string) ($payload['hostname'] ?? '');

            if ($expectedHostname !== '' && $actualHostname !== '' && strcasecmp($expectedHostname, $actualHostname) !== 0) {
                $auditPayload['ErrorCodes'] = json_encode(['hostname-mismatch']);
                $this->audit($auditPayload);

                return [
                    'success' => false,
                    'message' => 'Captcha verification failed due to hostname mismatch.',
                    'errors' => ['hostname-mismatch'],
                ];
            }

            $auditPayload['Outcome'] = 'passed';
            $this->audit($auditPayload);

            return ['success' => true];
        } catch (\Throwable $e) {
            report($e);

            $auditPayload['ErrorCodes'] = json_encode(['verification-exception']);
            $this->audit($auditPayload);

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

    private function audit(array $payload): void
    {
        try {
            $auditModelClass = '\\App\\Models\\TurnstileAudit';
            if (class_exists($auditModelClass)) {
                $auditModelClass::create($payload);
            }
        } catch (\Throwable $e) {
            // Audit write failures must never break authentication.
            report($e);
        }
    }
}
