<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ── Authentication ────────────────────────────────────────

    #[Test]
    public function login_response_never_exposes_password_hash(): void
    {
        User::factory()->create(['email' => 'test@test.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@test.com',
            'password' => 'password',
        ]);

        $content = $response->getContent();
        $this->assertStringNotContainsString('$2y$', $content, 'bcrypt hash must never appear in response');
        $this->assertStringNotContainsString('password', $response->json('data.user') ? json_encode($response->json('data.user')) : '');
    }

    #[Test]
    public function login_response_never_exposes_verification_code(): void
    {
        User::factory()->create([
            'email'             => 'test@test.com',
            'verification_code' => '123456',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@test.com',
            'password' => 'password',
        ]);

        $content = $response->getContent();
        $this->assertStringNotContainsString('123456', $content);
        $this->assertStringNotContainsString('verification_code', $content);
    }

    #[Test]
    public function login_response_never_exposes_remember_token(): void
    {
        User::factory()->create([
            'email'          => 'test@test.com',
            'remember_token' => 'secret_token_abc123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@test.com',
            'password' => 'password',
        ]);

        $content = $response->getContent();
        $this->assertStringNotContainsString('secret_token_abc123', $content);
        $this->assertStringNotContainsString('remember_token', $content);
    }

    #[Test]
    public function wrong_password_returns_401_not_422(): void
    {
        User::factory()->create(['email' => 'test@test.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@test.com',
            'password' => 'wrongpassword',
        ]);

        // Must be 401 (unauthorized), not 422 (validation)
        // 422 would leak that the email exists
        $response->assertStatus(401);
        $this->assertEquals('Invalid credentials', $response->json('message'));
    }

    #[Test]
    public function forgot_password_same_response_for_existing_and_nonexistent_email(): void
    {
        User::factory()->create(['email' => 'exists@test.com']);

        $existsResponse = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'exists@test.com',
        ]);

        $notExistsResponse = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'doesnotexist999@test.com',
        ]);

        // Both must return 200 — user enumeration protection
        $existsResponse->assertStatus(200);
        $notExistsResponse->assertStatus(200);
        $this->assertEquals(
            $existsResponse->json('success'),
            $notExistsResponse->json('success'),
            'Response must be identical to prevent user enumeration'
        );
    }

    #[Test]
    public function password_reset_wrong_code_returns_400_not_details(): void
    {
        User::factory()->create(['email' => 'test@test.com']);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email'                 => 'test@test.com',
            'code'                  => '000000',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400);
        // Must not expose internal details
        $this->assertArrayNotHasKey('trace', $response->json());
        $this->assertArrayNotHasKey('exception', $response->json());
    }

    #[Test]
    public function token_is_invalidated_after_logout(): void
    {
        $user  = User::factory()->create();
        $token = auth('api')->login($user);
        $headers = ['Authorization' => "Bearer {$token}"];

        // Token works before logout
        $this->getJson('/api/v1/profile', $headers)->assertStatus(200);

        // Logout
        $this->postJson('/api/v1/auth/logout', [], $headers)->assertStatus(200);

        // Token must not work after logout
        $this->getJson('/api/v1/profile', $headers)->assertStatus(401);
    }

    #[Test]
    public function password_is_stored_as_bcrypt_not_plaintext(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@test.com',
            'password'              => 'myplainpassword123',
            'password_confirmation' => 'myplainpassword123',
        ]);

        $user = User::where('email', 'test@test.com')->first();

        $this->assertNotEquals('myplainpassword123', $user->password);
        $this->assertTrue(Hash::check('myplainpassword123', $user->password));
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    #[Test]
    public function unauthenticated_request_returns_json_not_redirect(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401);
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));

        // Bug 001 fix — must NOT say "Route [login] not defined"
        $this->assertStringNotContainsString('Route [login]', $response->getContent());
        $this->assertStringNotContainsString('text/html', $response->headers->get('Content-Type') ?? '');
    }
}