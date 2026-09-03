<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityTest extends TestCase
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

    #[Test]
    public function unauthenticated_request_returns_json_not_html(): void
    {
        $response = $this->getJson('/api/v1/progress');

        $response->assertStatus(401)
                 ->assertHeader('Content-Type', 'application/json')
                 ->assertJson(['success' => false, 'message' => 'Unauthenticated.']);

        // Bug 001 fix — must NOT say "Route [login] not defined"
        $this->assertStringNotContainsString(
            'Route [login]',
            $response->getContent()
        );
    }

    #[Test]
    public function nonexistent_endpoint_returns_json_404_not_html(): void
    {
        $response = $this->getJson('/api/v1/this-does-not-exist');

        $response->assertStatus(404)
                 ->assertHeader('Content-Type', 'application/json')
                 ->assertJson(['success' => false, 'message' => 'Endpoint not found.']);

        // No stack trace exposed
        $this->assertArrayNotHasKey('trace', $response->json());
        $this->assertArrayNotHasKey('exception', $response->json());
    }

    #[Test]
    public function learner_cannot_access_any_admin_endpoint(): void
    {
        $headers = $this->authHeaders($this->learner);

        $adminRoutes = [
            ['GET',    '/api/v1/admin/users'],
            ['GET',    '/api/v1/admin/tracks'],
            ['POST',   '/api/v1/admin/tracks'],
            ['GET',    '/api/v1/admin/courses'],
            ['GET',    '/api/v1/admin/topics'],
            ['GET',    '/api/v1/admin/quizzes'],
            ['GET',    '/api/v1/admin/videos'],
            ['GET',    '/api/v1/admin/teams'],
        ];

        foreach ($adminRoutes as [$method, $route]) {
            $response = $this->json($method, $route, [], $headers);
            $this->assertEquals(403, $response->getStatusCode(),
                "Expected 403 for {$method} {$route} with learner token");
            $this->assertFalse($response->json('success'));
            $this->assertArrayNotHasKey('trace', $response->json());
        }
    }

    #[Test]
    public function error_responses_never_expose_stack_traces(): void
    {
        // Try to trigger various errors
        $headers = $this->authHeaders($this->admin);

        $response = $this->getJson('/api/v1/admin/users/999999', $headers);
        $response->assertStatus(404);
        $this->assertArrayNotHasKey('trace', $response->json());

        $response = $this->getJson('/api/v1/teams/999999', $headers);
        $response->assertStatus(404);
        $this->assertArrayNotHasKey('trace', $response->json());
    }

    #[Test]
    public function health_check_returns_all_systems_status(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
                 ->assertJsonPath('healthy', true)
                 ->assertJsonPath('checks.database.status', 'ok')
                 ->assertJsonPath('checks.cache.status', 'ok')
                 ->assertJsonPath('checks.storage.status', 'ok');

        $this->assertArrayHasKey('php', $response->json('checks.app'));
        $this->assertArrayHasKey('laravel', $response->json('checks.app'));
    }
}