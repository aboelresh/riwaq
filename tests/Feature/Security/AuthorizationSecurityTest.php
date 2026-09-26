<?php

namespace Tests\Feature\Security;

use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthorizationSecurityTest extends TestCase
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

    // ── Admin vs Learner ──────────────────────────────────────

    #[Test]
    public function learner_cannot_access_any_admin_endpoint(): void
    {
        $headers = $this->authHeaders($this->learner);

        $endpoints = [
            ['GET',    '/api/v1/admin/users'],
            ['GET',    '/api/v1/admin/tracks'],
            ['POST',   '/api/v1/admin/tracks'],
            ['GET',    '/api/v1/admin/courses'],
            ['POST',   '/api/v1/admin/courses'],
            ['GET',    '/api/v1/admin/topics'],
            ['POST',   '/api/v1/admin/topics'],
            ['GET',    '/api/v1/admin/quizzes'],
            ['POST',   '/api/v1/admin/quizzes'],
            ['GET',    '/api/v1/admin/videos'],
            ['GET',    '/api/v1/admin/teams'],
        ];

        foreach ($endpoints as [$method, $url]) {
            $response = $this->json($method, $url, [], $headers);
            $this->assertEquals(
                403,
                $response->getStatusCode(),
                "Expected 403 for learner on {$method} {$url}, got {$response->getStatusCode()}"
            );
            $this->assertFalse($response->json('success'), "success must be false for {$method} {$url}");
            $this->assertArrayNotHasKey('trace', $response->json());
        }
    }

    #[Test]
    public function unauthenticated_user_cannot_access_admin_endpoints(): void
    {
        $endpoints = [
            ['GET',  '/api/v1/admin/users'],
            ['GET',  '/api/v1/admin/tracks'],
            ['POST', '/api/v1/admin/tracks'],
        ];

        foreach ($endpoints as [$method, $url]) {
            $response = $this->json($method, $url);
            $this->assertEquals(401, $response->getStatusCode(),
                "Expected 401 for unauthenticated {$method} {$url}");
        }
    }

    #[Test]
    public function admin_can_access_admin_endpoints(): void
    {
        $headers  = $this->authHeaders($this->admin);
        $response = $this->getJson('/api/v1/admin/users', $headers);
        $response->assertStatus(200);
    }

    // ── Object-level Authorization (IDOR/BOLA) ────────────────

    #[Test]
    public function user_cannot_access_another_users_notification(): void
    {
        $otherUser = User::factory()->create();

        \App\Services\NotificationService::send(
            userId: $otherUser->id,
            type:   'test',
            title:  'Private',
            body:   'Private notification',
            data:   []
        );

        $notification = \App\Models\Notification::where('user_id', $otherUser->id)->first();

        // Try to read another user's notification
        $response = $this->postJson(
            "/api/v1/notifications/{$notification->id}/read",
            [],
            $this->authHeaders($this->learner)
        );

        $response->assertStatus(404); // IDOR protection — 404 not 403 to avoid confirmation
    }

   #[Test]
public function admin_track_delete_requires_admin_role(): void
{
    $track   = Track::create(['title' => 'Test', 'created_by' => $this->admin->id]);
    $headers = $this->authHeaders($this->learner);

    $response = $this->deleteJson("/api/v1/admin/tracks/{$track->id}", [], $headers);
    $response->assertStatus(403);
}

    #[Test]
    public function error_responses_never_expose_stack_traces(): void
    {
        $headers = $this->authHeaders($this->admin);

        // Try to access non-existent resources
        $this->getJson('/api/v1/admin/users/999999', $headers)
             ->assertStatus(404)
             ->assertJsonMissing(['trace'])
             ->assertJsonMissing(['exception'])
             ->assertJsonMissing(['file']);


        // Invalid route
        $this->getJson('/api/v1/this-does-not-exist')
             ->assertStatus(404)
             ->assertJsonMissing(['trace'])
             ->assertJsonMissing(['exception']);
    }
}