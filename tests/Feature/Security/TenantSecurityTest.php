<?php

namespace Tests\Feature\Security;

use App\Models\Course;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Track;
use App\Models\User;
use App\SaaS\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantSecurityTest extends TestCase
{
    use RefreshDatabase;

    private Organization $orgA;
    private Organization $orgB;
    private User         $adminA;
    private User         $adminB;
    private Plan         $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name'                   => 'Test',
            'slug'                   => 'test',
            'max_students'           => 100,
            'max_instructors'        => 10,
            'max_courses'            => 50,
            'max_tracks'             => 20,
            'max_storage_gb'         => 10,
            'max_ai_calls_per_month' => 1000,
        ]);

        $this->adminA = User::factory()->admin()->create();
        $this->adminB = User::factory()->admin()->create();

        $this->orgA = Organization::create([
            'name'      => 'Academy A',
            'slug'      => 'academy-a',
            'owner_id'  => $this->adminA->id,
            'plan_id'   => $this->plan->id,
            'is_active' => true,
        ]);

        $this->orgB = Organization::create([
            'name'      => 'Academy B',
            'slug'      => 'academy-b',
            'owner_id'  => $this->adminB->id,
            'plan_id'   => $this->plan->id,
            'is_active' => true,
        ]);

        $this->orgA->users()->attach($this->adminA->id, [
            'role' => 'owner', 'status' => 'active', 'joined_at' => now(),
        ]);
        $this->orgB->users()->attach($this->adminB->id, [
            'role' => 'owner', 'status' => 'active', 'joined_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        TenantContext::flush();
        parent::tearDown();
    }

    // ── Core Tenant Isolation ─────────────────────────────────

    #[Test]
    public function org_a_cannot_read_org_b_courses_via_global_scope(): void
    {
        TenantContext::actingAs($this->orgB);
        $courseB = Course::create([
            'title'           => 'Org B Secret Course',
            'created_by'      => $this->adminB->id,
            'organization_id' => $this->orgB->id,
        ]);
        TenantContext::flush();

        // Query as Org A — BelongsToTenant Global Scope must block access
        TenantContext::actingAs($this->orgA);
        $visible = Course::all();

        $this->assertCount(0, $visible);
        $this->assertFalse($visible->contains('id', $courseB->id),
            'Org A must never see Org B courses — BelongsToTenant Global Scope failed');
    }

    #[Test]
    public function org_b_cannot_read_org_a_tracks_via_global_scope(): void
    {
        TenantContext::actingAs($this->orgA);
        $trackA = Track::create([
            'title'           => 'Org A Secret Track',
            'created_by'      => $this->adminA->id,
            'organization_id' => $this->orgA->id,
        ]);
        TenantContext::flush();

        TenantContext::actingAs($this->orgB);
        $visible = Track::all();

        $this->assertCount(0, $visible);
        $this->assertFalse($visible->contains('id', $trackA->id));
    }

    #[Test]
    public function api_list_courses_does_not_return_other_tenant_data(): void
    {
        // Create Org B course
        TenantContext::actingAs($this->orgB);
        $courseB = Course::create([
            'title'           => 'Org B Only',
            'created_by'      => $this->adminB->id,
            'organization_id' => $this->orgB->id,
        ]);
        TenantContext::flush();

        // Query as Org A via API
        TenantContext::actingAs($this->orgA);
        $response = $this->actingAs($this->adminA, 'api')
            ->getJson('/api/v1/admin/courses');

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertNotContains($courseB->id, $ids,
            'API must not return Org B courses to Org A user');
    }

    #[Test]
    public function api_list_tracks_does_not_return_other_tenant_data(): void
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
        $ids = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertNotContains($trackB->id, $ids);
    }

    #[Test]
    public function new_records_auto_tagged_with_current_tenant(): void
    {
        TenantContext::actingAs($this->orgA);

        $course = Course::create([
            'title'      => 'Auto-tagged',
            'created_by' => $this->adminA->id,
        ]);

        $this->assertEquals(
            $this->orgA->id,
            $course->organization_id,
            'BelongsToTenant creating hook must auto-set organization_id'
        );
    }

    #[Test]
    public function without_tenant_scope_returns_all_tenants_data(): void
    {
        // System-level query — admin/reporting bypass
        TenantContext::actingAs($this->orgA);
        Course::create(['title' => 'A Course', 'created_by' => $this->adminA->id]);
        TenantContext::flush();

        TenantContext::actingAs($this->orgB);
        Course::create(['title' => 'B Course', 'created_by' => $this->adminB->id]);
        TenantContext::flush();

        $all = Course::withoutTenantScope()->get();
        $this->assertCount(2, $all, 'withoutTenantScope must see all tenants — for system admin use');
    }

    #[Test]
    public function user_not_in_org_cannot_act_as_that_org(): void
    {
        $outsider = User::factory()->create(); // Not in any org

        // Outsider tries to access Org B's data
        TenantContext::flush();
        TenantContext::actingAs($this->orgB);

        // Even with Org B context, admin routes require admin role
        $response = $this->actingAs($outsider, 'api')
            ->getJson('/api/v1/admin/courses');

        $response->assertStatus(403);
    }

    #[Test]
    public function switching_tenant_context_does_not_leak_previous_tenant_data(): void
    {
        // Create data in Org A
        TenantContext::actingAs($this->orgA);
        $courseA = Course::create([
            'title'           => 'Org A Course',
            'created_by'      => $this->adminA->id,
            'organization_id' => $this->orgA->id,
        ]);

        // Switch to Org B — Org A's data must not be visible
        TenantContext::flush();
        TenantContext::actingAs($this->orgB);

        $visible = Course::all();
        $this->assertFalse($visible->contains('id', $courseA->id),
            'After switching tenant context, previous tenant data must not be visible');
    }
}