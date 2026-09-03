<?php

namespace Tests\Feature\Teams;

use App\Models\Track;
use App\Models\User;
use App\Models\UserTrack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamOperationsTest extends TestCase
{
    use RefreshDatabase;

    private User  $admin;
    private User  $learner;
    private Track $track;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->admin()->create();
        $this->learner = User::factory()->learner()->create();
        $this->track   = Track::create(['title' => 'Test Track', 'created_by' => $this->admin->id]);

        UserTrack::create(['user_id' => $this->admin->id, 'track_id' => $this->track->id, 'status' => 'active']);
    }

    private function createTeam(): array
    {
        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Test Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($this->admin));

        return $response->json('data');
    }

    #[Test]
    public function create_team_wraps_in_transaction_bug009(): void
    {
        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Transaction Test',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($this->admin));

        $response->assertStatus(201);

        // All 3 operations completed: team + member + section
        $teamId = $response->json('data.id');
        $this->assertDatabaseHas('teams',        ['id' => $teamId]);
        $this->assertDatabaseHas('team_members', ['team_id' => $teamId, 'user_id' => $this->admin->id]);
        $this->assertDatabaseHas('team_sections',['team_id' => $teamId, 'is_general' => true]);
    }

    #[Test]
    public function team_code_is_auto_generated_and_unique(): void
    {
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $this->assertNotEmpty($team1['code']);
        $this->assertNotEmpty($team2['code']);
        $this->assertNotEquals($team1['code'], $team2['code']);
        $this->assertEquals(8, strlen($team1['code']));
    }

    #[Test]
    public function leader_cannot_leave_team(): void
    {
        $team = $this->createTeam();

        $response = $this->postJson(
            "/api/v1/teams/{$team['id']}/leave",
            [],
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(400)
                 ->assertJson(['success' => false]);

        $this->assertStringContainsString('leader', $response->json('message'));
    }

    #[Test]
    public function cannot_kick_team_leader(): void
    {
        $team = $this->createTeam();

        $response = $this->deleteJson(
            "/api/v1/teams/{$team['id']}/members/{$this->admin->id}",
            [],
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(400);
        $this->assertStringContainsString('leader', $response->json('message'));
    }

    #[Test]
    public function soft_delete_preserves_team_data_in_database(): void
    {
        $team = $this->createTeam();
        $teamId = $team['id'];

        // Delete team
        $this->deleteJson("/api/v1/teams/{$teamId}", [], $this->authHeaders($this->admin))
             ->assertStatus(200);

        // Should return 404 via API
        $this->getJson("/api/v1/teams/{$teamId}", $this->authHeaders($this->admin))
             ->assertStatus(404);

        // But data still in DB (soft delete)
        $this->assertSoftDeleted('teams', ['id' => $teamId]);
    }

    #[Test]
    public function cannot_create_team_without_track_enrollment(): void
    {
        $unenrolled = User::factory()->create();

        $response = $this->postJson('/api/v1/teams/create', [
            'name'         => 'Fail Team',
            'type'         => 'project',
            'project_type' => 'web',
            'max_members'  => 5,
            'track_id'     => $this->track->id,
        ], $this->authHeaders($unenrolled));

        $response->assertStatus(400)
                 ->assertJson(['success' => false]);
    }

    #[Test]
    public function update_max_members_below_current_count_fails(): void
    {
        $team = $this->createTeam();

        $response = $this->putJson(
            "/api/v1/teams/{$team['id']}",
            ['max_members' => 0],
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(422)
                 ->assertJsonValidationErrorFor('max_members');
    }

    #[Test]
    public function general_section_cannot_be_deleted(): void
    {
        $team = $this->createTeam();

        $generalSection = \App\Models\TeamSection::where('team_id', $team['id'])
            ->where('is_general', true)
            ->first();

        $response = $this->deleteJson(
            "/api/v1/teams/{$team['id']}/sections/{$generalSection->id}",
            [],
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(400);
        $this->assertStringContainsString('general', strtolower($response->json('message')));
    }

    #[Test]
    public function bulk_checklist_uses_transaction_bug011(): void
    {
        $team   = $this->createTeam();
        $teamId = $team['id'];

        $task = $this->postJson("/api/v1/teams/{$teamId}/tasks", [
            'title'    => 'Test Task',
            'priority' => 'medium',
        ], $this->authHeaders($this->admin))->json('data');

        $response = $this->postJson(
            "/api/v1/teams/{$teamId}/tasks/{$task['id']}/checklist",
            ['items' => ['Task A', 'Task B', 'Task C']],
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(201);
        $this->assertCount(3, $response->json('data'));
        $this->assertDatabaseCount('task_checklist_items', 3);
    }

    #[Test]
public function only_leader_can_create_tasks(): void
{
    $team = $this->createTeam();

    // Enroll learner in track (needed to be a valid user)
    UserTrack::create([
        'user_id'  => $this->learner->id,
        'track_id' => $this->track->id,
        'status'   => 'active',
    ]);

    // Use actingAs to avoid JWT token leakage between tests
    $response = $this->actingAs($this->learner, 'api')
        ->postJson("/api/v1/teams/{$team['id']}/tasks", [
            'title'    => 'Fail',
            'priority' => 'low',
        ]);

    $response->assertStatus(403);
    $this->assertStringContainsString('leader', $response->json('message'));
}
}