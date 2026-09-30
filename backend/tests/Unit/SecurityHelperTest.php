<?php

namespace Tests\Unit;

use App\Support\SecurityHelper;
use Tests\TestCase;

class SecurityHelperTest extends TestCase
{
    /** UT-101 */
    public function test_ut101_validate_email_format_rejects_typos()
    {
        $this->assertFalse(SecurityHelper::validateEmailFormat('test@com'));
        $this->assertFalse(SecurityHelper::validateEmailFormat('@@domain.com'));
        $this->assertTrue(SecurityHelper::validateEmailFormat('user@example.com'));
    }

    /** UT-102 */
    public function test_ut102_validate_password_strength_rejects_weak()
    {
        $this->assertFalse(SecurityHelper::validatePasswordStrength('abc123'));
        $this->assertFalse(SecurityHelper::validatePasswordStrength('abcdefgh'));
        $this->assertTrue(SecurityHelper::validatePasswordStrength('abc@1234'));
    }

    /** UT-103 */
    public function test_ut103_hash_user_password_returns_secure_hash()
    {
        $hash = SecurityHelper::hashUserPassword('MySecret@123');
        $this->assertGreaterThanOrEqual(60, strlen($hash));
        $this->assertStringStartsWith('$2y$', $hash);
    }

    /** UT-104 */
    public function test_ut104_verify_password_match()
    {
        $hash = SecurityHelper::hashUserPassword('MySecret@123');
        $this->assertTrue(SecurityHelper::verifyPasswordMatch('MySecret@123', $hash));
        $this->assertFalse(SecurityHelper::verifyPasswordMatch('WrongPass!', $hash));
    }

    /** UT-105 */
    public function test_ut105_generate_session_token_is_secure_and_unique()
    {
        $tokenA = SecurityHelper::generateSessionToken(1);
        $tokenB = SecurityHelper::generateSessionToken(1);

        $this->assertEquals(64, strlen($tokenA));
        $this->assertNotEquals($tokenA, $tokenB);
    }

    /** UT-106 */
    public function test_ut106_is_token_expired()
    {
        $pastTime   = time() - 3600;
        $futureTime = time() + 3600;

        $this->assertTrue(SecurityHelper::isTokenExpired($pastTime));
        $this->assertFalse(SecurityHelper::isTokenExpired($futureTime));
    }

    /** UT-107 */
    public function test_ut107_sanitize_html_input_strips_script()
    {
        $input  = '<script>malicious()</script>Hello';
        $output = SecurityHelper::sanitizeHtmlInput($input);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertEquals('Hello', $output);
    }
}