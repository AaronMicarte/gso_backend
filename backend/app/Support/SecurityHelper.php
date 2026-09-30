<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;

class SecurityHelper
{
    /** UT-101 */
    public static function validateEmailFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** UT-102 */
    public static function validatePasswordStrength(string $pwd): bool
    {
        return strlen($pwd) >= 8 && preg_match('/[^A-Za-z0-9]/', $pwd) === 1;
    }

    /** UT-103 */
    public static function hashUserPassword(string $plainText): string
    {
        return Hash::make($plainText);
    }

    /** UT-104 */
    public static function verifyPasswordMatch(string $plain, string $hash): bool
    {
        return Hash::check($plain, $hash);
    }

    /** UT-105 */
    public static function generateSessionToken(int $userId): string
    {
        return hash('sha256', $userId . '|' . random_bytes(32) . '|' . microtime(true));
    }

    /** UT-106 */
    public static function isTokenExpired(int $timestamp): bool
    {
        return $timestamp < time();
    }

    /** UT-107 */
    public static function sanitizeHtmlInput(string $input): string
    {
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
        return strip_tags($clean);
    }
}