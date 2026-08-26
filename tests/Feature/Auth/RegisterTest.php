<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register_successfully(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Ahmed Test',
            'email'                 => 'ahmed@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => ['user', 'token', 'token_type'],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ahmed@test.com',
            'role'  => 'learner',
        ]);
    }

    #[Test]
    public function email_is_stored_in_lowercase(): void
    {
        Queue::fake();

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'TEST@EXAMPLE.COM',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'TEST@EXAMPLE.COM']);
    }

    #[Test]
    public function duplicate_email_returns_422(): void
    {
        Queue::fake();

        User::factory()->create(['email' => 'taken@test.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Another User',
            'email'                 => 'taken@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.email.0', 'This email is already registered.');
    }

    #[Test]
    public function password_confirmation_is_required(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'     => 'Test',
            'email'    => 'new@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.password.0', 'Password confirmation does not match.');
    }

    #[Test]
    public function welcome_notification_is_created_after_register(): void
    {
        Queue::fake();

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Notified User',
            'email'                 => 'notify@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'notify@test.com')->first();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type'    => 'welcome',
        ]);
    }

    #[Test]
    public function verification_email_job_is_dispatched(): void
    {
        Queue::fake();

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Queue Test',
            'email'                 => 'queue@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        Queue::assertPushed(\App\Jobs\SendVerificationEmailJob::class);
    }
}