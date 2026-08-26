<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['user', 'token', 'token_type'],
                 ])
                 ->assertJson(['success' => true]);
    }

    #[Test]
    public function login_is_case_insensitive_on_email(): void
    {
        User::factory()->create(['email' => 'Test@Example.COM']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['success' => false, 'message' => 'Invalid credentials']);
    }

    #[Test]
    public function login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
                 ->assertJsonStructure(['success', 'message', 'errors']);
    }

    #[Test]
    public function response_does_not_expose_sensitive_fields(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $userData = $response->json('data.user');
        $this->assertArrayNotHasKey('password', $userData);
        $this->assertArrayNotHasKey('verification_code', $userData);
        $this->assertArrayNotHasKey('remember_token', $userData);
    }
}