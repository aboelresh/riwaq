<?php

namespace Tests\Feature\Admin;

use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TrackTest extends TestCase
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
    public function admin_can_list_tracks(): void
    {
        Track::create(['title' => 'Web Dev', 'created_by' => $this->admin->id]);
        Track::create(['title' => 'Mobile', 'created_by' => $this->admin->id]);

        $response = $this->getJson('/api/v1/admin/tracks', $this->authHeaders($this->admin));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure(['data', 'meta']);
    }

    #[Test]
    public function admin_can_create_a_track(): void
    {
        $response = $this->postJson('/api/v1/admin/tracks', [
            'title'       => 'New Track',
            'description' => 'Track description',
        ], $this->authHeaders($this->admin));

        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'New Track');

        $this->assertDatabaseHas('tracks', ['title' => 'New Track']);
    }

    #[Test]
    public function admin_can_update_a_track(): void
    {
        $track = Track::create(['title' => 'Old Title', 'created_by' => $this->admin->id]);

        $response = $this->putJson("/api/v1/admin/tracks/{$track->id}", [
            'title' => 'New Title',
        ], $this->authHeaders($this->admin));

        $response->assertStatus(200)
                 ->assertJsonPath('data.title', 'New Title');
    }

    #[Test]
    public function admin_cannot_delete_track_with_enrolled_users(): void
    {
        $track = Track::create(['title' => 'Enrolled Track', 'created_by' => $this->admin->id]);

        \App\Models\UserTrack::create([
            'user_id'  => $this->learner->id,
            'track_id' => $track->id,
            'status'   => 'active',
        ]);

        $response = $this->deleteJson(
            "/api/v1/admin/tracks/{$track->id}",
            [],
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(409)
                 ->assertJson(['success' => false]);
    }

    #[Test]
    public function learner_cannot_access_admin_tracks(): void
    {
        $response = $this->getJson('/api/v1/admin/tracks', $this->authHeaders($this->learner));

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_cannot_access_admin_tracks(): void
    {
        $response = $this->getJson('/api/v1/admin/tracks');

        $response->assertStatus(401);
    }

    #[Test]
    public function create_track_requires_title(): void
    {
        $response = $this->postJson('/api/v1/admin/tracks', [
            'description' => 'No title',
        ], $this->authHeaders($this->admin));

        $response->assertStatus(422)
                 ->assertJsonPath('errors.title.0', 'Track title is required.');
    }
}