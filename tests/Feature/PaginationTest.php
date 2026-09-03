<?php

namespace Tests\Feature;

use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin with searchable name/email
        $this->admin = User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin_search@test.com',
        ]);

        // Clear cache before each test to avoid stale cached responses
        Cache::flush();

        // Create 15 tracks
        for ($i = 1; $i <= 15; $i++) {
            Track::create([
                'title'       => "Track {$i}",
                'created_by'  => $this->admin->id,
            ]);
        }
    }

    #[Test]
    public function public_tracks_list_is_paginated_with_12_per_page(): void
    {
        $response = $this->getJson('/api/v1/tracks');

        $response->assertStatus(200)
                 ->assertJsonPath('meta.per_page', 12)
                 ->assertJsonPath('meta.current_page', 1)
                 ->assertJsonStructure([
                     'data',
                     'meta' => ['current_page', 'per_page', 'total', 'last_page'],
                 ]);

        $this->assertCount(12, $response->json('data'));
    }

    #[Test]
    public function page_2_returns_remaining_tracks(): void
    {
        $response = $this->getJson('/api/v1/tracks?page=2');

        $response->assertStatus(200)
                 ->assertJsonPath('meta.current_page', 2);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function public_courses_list_is_paginated_with_15_per_page(): void
    {
        $response = $this->getJson('/api/v1/courses');

        $response->assertStatus(200)
                 ->assertJsonPath('meta.per_page', 15);
    }

    #[Test]
    public function admin_tracks_paginated_with_20_per_page(): void
    {
        $response = $this->getJson('/api/v1/admin/tracks', $this->authHeaders($this->admin));

        $response->assertStatus(200)
                 ->assertJsonPath('meta.per_page', 20);
    }

    #[Test]
    public function admin_users_list_supports_search_filter(): void
    {
        $response = $this->getJson(
            '/api/v1/admin/users?search=admin_search',
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(200);
        $this->assertGreaterThan(0, count($response->json('data')));
    }
}