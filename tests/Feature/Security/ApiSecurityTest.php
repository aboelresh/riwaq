<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $learner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->admin()->create();
        $this->learner = User::factory()->learner()->create();
    }

    // ── HTTP Status Codes ─────────────────────────────────────

    #[Test]
    public function nonexistent_endpoint_returns_404_json_not_html(): void
    {
        $response = $this->getJson('/api/v1/this-endpoint-does-not-exist-at-all');

        $response->assertStatus(404);
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals('Endpoint not found.', $response->json('message'));
        $this->assertFalse($response->json('success'));
    }

    #[Test]
    public function wrong_http_method_returns_405_json(): void
    {
        $response = $this->deleteJson('/api/v1/tracks'); // DELETE not allowed on collection

        $response->assertStatus(405);
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function api_returns_json_content_type_always(): void
    {
        $responses = [
            $this->getJson('/api/v1/this-does-not-exist'),
            $this->getJson('/api/v1/profile'),          // 401
            $this->getJson('/api/v1/admin/tracks'),     // 401
        ];

        foreach ($responses as $response) {
            $this->assertStringContainsString(
                'application/json',
                $response->headers->get('Content-Type'),
                'All API responses must be JSON — never HTML'
            );
        }
    }

    // ── Sensitive Data Leakage ────────────────────────────────

    #[Test]
    public function profile_endpoint_hides_all_sensitive_fields(): void
    {
        $user = User::factory()->create([
            'verification_code'            => '123456',
            'verification_code_expires_at' => now()->addMinutes(15),
            'remember_token'               => 'secret_remember_token',
        ]);

        $response = $this->getJson('/api/v1/profile', $this->authHeaders($user));

        $response->assertStatus(200);
        $data = $response->json('data');

        $forbidden = ['password', 'verification_code', 'verification_code_expires_at', 'remember_token'];
        foreach ($forbidden as $field) {
            $this->assertArrayNotHasKey($field, $data, "{$field} must never appear in profile response");
        }

        // Verify code value doesn't appear in response content
        $this->assertStringNotContainsString('123456', $response->getContent());
        $this->assertStringNotContainsString('secret_remember_token', $response->getContent());
    }

    #[Test]
    public function admin_users_list_hides_sensitive_fields(): void
    {
        $response = $this->getJson('/api/v1/admin/users', $this->authHeaders($this->admin));

        $response->assertStatus(200);

        foreach ($response->json('data') as $user) {
            $this->assertArrayNotHasKey('password',          $user, 'password hash must never appear');
            $this->assertArrayNotHasKey('verification_code', $user, 'verification_code must never appear');
            $this->assertArrayNotHasKey('remember_token',    $user, 'remember_token must never appear');
        }
    }

    #[Test]
    public function error_responses_never_expose_internal_info(): void
    {
        // Trigger various errors
        $responses = [
            $this->getJson('/api/v1/admin/users/999999', $this->authHeaders($this->admin)),
            $this->getJson('/api/v1/tracks/999999'),
            $this->getJson('/api/v1/not-a-real-route'),
        ];

        foreach ($responses as $response) {
            $content = $response->json();
            $this->assertArrayNotHasKey('trace',     $content, 'Stack trace must never appear');
            $this->assertArrayNotHasKey('exception', $content, 'Exception class must never appear');
            $this->assertArrayNotHasKey('file',      $content, 'File path must never appear');
            $this->assertArrayNotHasKey('line',      $content, 'Line number must never appear');
        }
    }

    // ── Rate Limiting ─────────────────────────────────────────

    #[Test]
    public function ai_chat_enforces_per_user_rate_limit(): void
    {
        $user    = User::factory()->create();
        $headers = $this->authHeaders($user);

        // Simulate being at the limit via cache
        \Illuminate\Support\Facades\Cache::put(
            "ai_chat_limit:{$user->id}",
            30, // At the limit
            3600
        );

        $response = $this->postJson('/api/v1/ai/chat', [
            'message' => 'Hello',
            'history' => [],
        ], $headers);

        $response->assertStatus(429);
        $this->assertFalse($response->json('success'));
        $this->assertStringContainsString('Rate limit', $response->json('message'));
    }

    // ── Security Headers ──────────────────────────────────────

    #[Test]
    public function responses_include_security_headers(): void
    {
        $response = $this->getJson('/api/v1/health');

        $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
        $this->assertNotNull($response->headers->get('Referrer-Policy'));
    }

    // ── CORS ──────────────────────────────────────────────────

    #[Test]
    public function options_preflight_returns_cors_headers(): void
    {
        $response = $this->options('/api/v1/auth/login', [], [
            'Origin'                         => 'http://localhost:3000',
            'Access-Control-Request-Method'  => 'POST',
            'Access-Control-Request-Headers' => 'Content-Type,Authorization',
        ]);

        // Should get a valid CORS preflight response
        $this->assertContains($response->getStatusCode(), [200, 204]);
    }
}