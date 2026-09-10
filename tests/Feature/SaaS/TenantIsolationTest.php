<?php

namespace Tests\Feature\SaaS;

use App\Models\Course;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Track;
use App\Models\User;
use App\SaaS\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Organization $orgA;
    private Organization $orgB;
    private User         $adminA;
    private User         $adminB;
    private User         $studentA;
    private Plan         $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name'                   => 'Test Plan',
            'slug'                   => 'test',
            'max_students'           => 100,
            'max_instructors'        => 10,
            'max_courses'            => 50,
            'max_tracks'             => 20,
            'max_storage_gb'         => 10,
            'max_ai_calls_per_month' => 1000,
        ]);

        $this->adminA   = User::factory()->admin()->create();
        $this->adminB   = User::factory()->admin()->create();
        $this->studentA = User::factory()->learner()->create();

        $this->orgA = Organization::create([
            'name'     => 'Academy A',
            'slug'     => 'academy-a',
            'owner_id' => $this->adminA->id,
            'plan_id'  => $this->plan->id,
            'is_active'=> true,
        ]);

        $this->orgB = Organization::create([
            'name'     => 'Academy B',
            'slug'     => 'academy-b',
            'owner_id' => $this->adminB->id,
            'plan_id'  => $this->plan->id,
            'is_active'=> true,
        ]);

        // Add members
        $this->orgA->users()->attach($this->adminA->id, [
            'role' => 'owner', 'status' => 'active', 'joined_at' => now(),
        ]);
        $this->orgB->users()->attach($this->adminB->id, [
            'role' => 'owner', 'status' => 'active', 'joined_at' => now(),
        ]);
        $this->orgA->users()->attach($this->studentA->id, [
            'role' => 'student', 'status' => 'active', 'joined_at' => now(),
        ]);
    }

    private function actingAsWithOrg(User $user, Organization $org): static
    {
        TenantContext::flush();
        TenantContext::actingAs($org);
        return $this->actingAs($user, 'api');
    }

    // ── Core Isolation ──────────────────────────────────────────

    #[Test]
    public function academy_a_cannot_see_courses_of_academy_b(): void
    {
        // Create course in Org B
        TenantContext::actingAs($this->orgB);
        $courseB = Course::create([
            'title'           => 'Secret Course B',
            'created_by'      => $this->adminB->id,
            'organization_id' => $this->orgB->id,
        ]);
        TenantContext::flush();

        // Query as Org A — must NOT see Org B's course
        TenantContext::actingAs($this->orgA);
        $courses = Course::all();

        $this->assertCount(0, $courses, 'Academy A should see 0 courses — not Org B data');
        $this->assertFalse(
            $courses->contains('id', $courseB->id),
            'BelongsToTenant Global Scope must prevent cross-tenant data leak'
        );
    }

    #[Test]
    public function academy_b_cannot_see_tracks_of_academy_a(): void
    {
        TenantContext::actingAs($this->orgA);
        $trackA = Track::create([
            'title'           => 'Track A',
            'created_by'      => $this->adminA->id,
            'organization_id' => $this->orgA->id,
        ]);
        TenantContext::flush();

        TenantContext::actingAs($this->orgB);
        $tracks = Track::all();

        $this->assertCount(0, $tracks);
        $this->assertFalse($tracks->contains('id', $trackA->id));
    }

    #[Test]
    public function new_course_auto_gets_current_tenant_organization_id(): void
    {
        TenantContext::actingAs($this->orgA);

        $course = Course::create([
            'title'      => 'Auto-tagged Course',
            'created_by' => $this->adminA->id,
        ]);

        $this->assertEquals(
            $this->orgA->id,
            $course->organization_id,
            'BelongsToTenant creating hook must auto-set organization_id'
        );
    }

    #[Test]
    public function without_tenant_scope_returns_all_records(): void
    {
        // Create in Org A
        TenantContext::actingAs($this->orgA);
        Course::create(['title' => 'Course A', 'created_by' => $this->adminA->id]);
        TenantContext::flush();

        // Create in Org B
        TenantContext::actingAs($this->orgB);
        Course::create(['title' => 'Course B', 'created_by' => $this->adminB->id]);
        TenantContext::flush();

        // System-level query (admin/reporting) — sees all
        $all = Course::withoutTenantScope()->get();
        $this->assertCount(2, $all, 'withoutTenantScope must bypass isolation for system queries');
    }

    // ── API Isolation ───────────────────────────────────────────

    #[Test]
    public function api_list_courses_only_returns_current_tenant_courses(): void
    {
        // Create course in Org B
        TenantContext::actingAs($this->orgB);
        $courseB = Course::create([
            'title'           => 'Org B Course',
            'created_by'      => $this->adminB->id,
            'organization_id' => $this->orgB->id,
        ]);
        TenantContext::flush();

        // Hit API as Org A user
        TenantContext::actingAs($this->orgA);
        $response = $this->actingAs($this->adminA, 'api')
            ->getJson('/api/v1/admin/courses');

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertNotContains(
            $courseB->id,
            $ids->toArray(),
            'API must not return Org B courses when called by Org A user'
        );
    }

    #[Test]
    public function api_list_tracks_only_returns_current_tenant_tracks(): void
    {
        TenantContext::actingAs($this->orgB);
        $trackB = Track::create([
            'title'           => 'Org B Track',
            'created_by'      => $this->adminB->id,
            'organization_id' => $this->orgB->id,
        ]);
        TenantContext::flush();

        TenantContext::actingAs($this->orgA);
        $response = $this->actingAs($this->adminA, 'api')
            ->getJson('/api/v1/admin/tracks');

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertNotContains($trackB->id, $ids->toArray());
    }

    // ── Entitlement ─────────────────────────────────────────────

    #[Test]
    public function entitlement_blocks_course_creation_when_limit_reached(): void
    {
        // Set plan max_courses = 1
        $this->plan->update(['max_courses' => 1]);

        TenantContext::actingAs($this->orgA);

        // Create first course — should succeed
        Course::create([
            'title'           => 'First Course',
            'created_by'      => $this->adminA->id,
            'organization_id' => $this->orgA->id,
        ]);

        // Try to create second course via API — should be blocked
        $response = $this->actingAs($this->adminA, 'api')
            ->postJson('/api/v1/admin/courses', [
                'title'       => 'Second Course',
                'description' => 'Should be blocked',
            ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('limit', strtolower($response->json('message')));
    }

    #[Test]
    public function entitlement_allows_course_creation_within_limit(): void
    {
        $this->plan->update(['max_courses' => 10]);

        TenantContext::actingAs($this->orgA);

        $response = $this->actingAs($this->adminA, 'api')
            ->postJson('/api/v1/admin/courses', [
                'title'       => 'Allowed Course',
                'description' => 'Within plan limit',
            ]);

        $response->assertStatus(201);
    }

    // ── Membership ──────────────────────────────────────────────

    #[Test]
    public function user_in_org_a_cannot_access_org_b_via_api(): void
    {
        // studentA belongs to Org A only
        // Try to access Org B data by switching context (simulated attack)
        TenantContext::flush();
        TenantContext::actingAs($this->orgB); // attacker sets org B context

        $response = $this->actingAs($this->studentA, 'api')
            ->getJson('/api/v1/admin/courses');

        // studentA has no admin role anywhere — should get 403
        $response->assertStatus(403);
    }

    #[Test]
    public function single_license_mode_always_resolves_to_org_1(): void
    {
        config(['app.single_license_mode' => true,
                'app.single_license_organization_id' => $this->orgA->id]);

        TenantContext::flush();
        TenantContext::setDefault();

        $this->assertEquals(
            $this->orgA->id,
            TenantContext::currentId(),
            'Single License Mode must always resolve to the configured organization'
        );
    }

    protected function tearDown(): void
    {
        TenantContext::flush();
        parent::tearDown();
    }
}