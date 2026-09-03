<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function createNotification(int $userId, array $override = []): void
    {
        \App\Services\NotificationService::send(
            userId: $userId,
            type:   $override['type']  ?? 'test',
            title:  $override['title'] ?? 'Test Notification',
            body:   $override['body']  ?? 'Test body',
            data:   $override['data']  ?? []
        );
    }

    #[Test]
    public function register_creates_welcome_notification(): void
    {
        Queue::fake();

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Notify Test',
            'email'                 => 'notify@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(201);

        $user = User::where('email', 'notify@test.com')->first();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type'    => 'welcome',
        ]);
    }

    #[Test]
    public function mark_all_read_is_idempotent(): void
    {
        $headers = $this->authHeaders($this->user);

        // Create notification via NotificationService (correct way)
        $this->createNotification($this->user->id);

        // Run twice — both must return 200 (idempotent)
        $this->postJson('/api/v1/notifications/read-all', [], $headers)
             ->assertStatus(200)
             ->assertJson(['success' => true]);

        $this->postJson('/api/v1/notifications/read-all', [], $headers)
             ->assertStatus(200)
             ->assertJson(['success' => true]);
    }

    #[Test]
    public function cannot_mark_other_users_notification_as_read(): void
    {
        $otherUser = User::factory()->create();

        // Create notification for other user via NotificationService
        $this->createNotification($otherUser->id);

        $notification = \App\Models\Notification::where('user_id', $otherUser->id)->first();

        $this->assertNotNull($notification, 'Notification should exist for other user');

        // Try to mark other user's notification as read — IDOR protection
        $response = $this->postJson(
            "/api/v1/notifications/{$notification->id}/read",
            [],
            $this->authHeaders($this->user)
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function unread_count_returns_numeric_value(): void
    {
        $response = $this->getJson(
            '/api/v1/notifications/unread-count',
            $this->authHeaders($this->user)
        );

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['count']]);

        $this->assertIsInt($response->json('data.count'));
    }

    #[Test]
    public function list_notifications_returns_array(): void
    {
        $this->createNotification($this->user->id);

        $response = $this->getJson('/api/v1/notifications', $this->authHeaders($this->user));

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);

        $this->assertIsArray($response->json('data'));
        $this->assertGreaterThan(0, count($response->json('data')));
    }
}