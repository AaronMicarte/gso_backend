<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebaseAccessTokenService
{
    private const CACHE_KEY = 'fcm_oauth_access_token';

    public function getAccessToken(): string
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $serviceAccount = $this->readServiceAccount();
        $jwt = $this->createJwt($serviceAccount);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Failed to fetch Firebase access token: ' . $response->body());
        }

        $payload = $response->json();
        $accessToken = (string) ($payload['access_token'] ?? '');
        $expiresIn = (int) ($payload['expires_in'] ?? 3600);

        if ($accessToken === '') {
            throw new RuntimeException('Firebase access token response did not include access_token');
        }

        Cache::put(self::CACHE_KEY, $accessToken, now()->addSeconds(max(60, $expiresIn - 120)));

        return $accessToken;
    }

    /**
     * @return array<string, string>
     */
    private function readServiceAccount(): array
    {
        $path = (string) env('FCM_SERVICE_ACCOUNT_JSON', '');
        if ($path === '' || !is_file($path)) {
            throw new RuntimeException('FCM_SERVICE_ACCOUNT_JSON is missing or points to a non-existent file');
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException('Unable to read FCM service account file');
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid JSON in FCM service account file');
        }

        foreach (['client_email', 'private_key', 'token_uri'] as $requiredField) {
            if (empty($decoded[$requiredField]) || !is_string($decoded[$requiredField])) {
                throw new RuntimeException('Missing required field in FCM service account file: ' . $requiredField);
            }
        }

        return $decoded;
    }

    /**
     * @param array<string, string> $serviceAccount
     */
    private function createJwt(array $serviceAccount): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $now = time();

        $claimSet = [
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $base64Header = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $base64ClaimSet = $this->base64UrlEncode(json_encode($claimSet, JSON_UNESCAPED_SLASHES));
        $unsignedJwt = $base64Header . '.' . $base64ClaimSet;

        $signature = '';
        $signed = openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);
        if (!$signed) {
            throw new RuntimeException('Failed to sign Firebase JWT');
        }

        return $unsignedJwt . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
