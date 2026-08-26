<?php

namespace Tests\Feature\Teams;

use App\Models\Track;
use App\Models\User;
use App\Models\UserTrack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    private User  $admin;
    private Track $track;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->track = Track::create([
            'title'      => 'Test Track',
            'created_by' => $this->admin->id,
        ]);

        // Enroll admin in track (required to create team)
        UserTrack::create([
            'user_id'  => $this->admin->id,
            'track_id' => $this->track->id,
            'status'   => 'active',
        ]);
    }

    #[Test]
    public function authenticated_user_can_create_a_team(): void
    {
        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Test Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($this->admin));

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJsonPath('data.name', 'Test Team');

        $this->assertDatabaseHas('teams', ['name' => 'Test Team']);
    }

    #[Test]
    public function general_section_is_auto_created_with_team(): void
    {
        $this->postJson('/api/v1/teams/create', [
            'name'         => 'Section Test Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($this->admin));

        $this->assertDatabaseHas('team_sections', ['name' => 'General', 'is_general' => 1]);
    }

    #[Test]
    public function creator_is_auto_added_as_member(): void
    {
        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Member Test Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($this->admin));

        $teamId = $response->json('data.id');

        $this->assertDatabaseHas('team_members', [
            'team_id' => $teamId,
            'user_id' => $this->admin->id,
        ]);
    }

    #[Test]
    public function team_requires_track_enrollment(): void
    {
        $unenrolledUser = User::factory()->create();

        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Fail Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($unenrolledUser));

        $response->assertStatus(400)
                 ->assertJson(['success' => false]);
    }

    #[Test]
    public function create_team_requires_all_fields(): void
    {
        $response = $this->postJson('/api/v1/teams/create', [
            'name' => 'Incomplete Team',
        ], $this->authHeaders($this->admin));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors']);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_team(): void
    {
        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Unauth Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ]);

        $response->assertStatus(401);
    }
}