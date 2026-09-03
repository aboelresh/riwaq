<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();

        Track::create(['title' => 'HTML Fundamentals', 'created_by' => $this->user->id]);
        Course::create(['title' => 'HTML Basics', 'created_by' => $this->user->id]);
    }

    #[Test]
    public function search_returns_structured_response(): void
    {
        $response = $this->getJson('/api/v1/search?q=html', $this->authHeaders($this->user));

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['tracks', 'courses', 'topics', 'teams']]);
    }

    #[Test]
    public function search_finds_matching_tracks(): void
    {
        $response = $this->getJson('/api/v1/search?q=html', $this->authHeaders($this->user));

        $response->assertStatus(200);
        $this->assertGreaterThan(0, count($response->json('data.tracks')));
    }

    #[Test]
    public function search_with_single_char_returns_empty(): void
    {
        $response = $this->getJson('/api/v1/search?q=h', $this->authHeaders($this->user));

        $response->assertStatus(200)
                 ->assertJsonPath('data.tracks', [])
                 ->assertJsonPath('data.courses', [])
                 ->assertJsonPath('data.topics', [])
                 ->assertJsonPath('data.teams', []);
    }

    #[Test]
    public function search_response_has_no_malformed_json_keys(): void
    {
        $response = $this->getJson('/api/v1/search?q=html', $this->authHeaders($this->user));

        $content = $response->getContent();

        // Search fix: no columns that don't exist (like "track_id" or "icon")
        $this->assertStringNotContainsString('"\"track_id\""', $content);
        $this->assertStringNotContainsString('"\"icon\""', $content);
    }

    #[Test]
    public function search_handles_sql_special_characters_safely(): void
    {
        // % would be a wildcard in LIKE — must be handled safely
        $response = $this->getJson('/api/v1/search?q=%25', $this->authHeaders($this->user));
        $response->assertStatus(200);

        // Single quote — potential SQL injection
        $response = $this->getJson("/api/v1/search?q='", $this->authHeaders($this->user));
        $response->assertStatus(200);
    }

    #[Test]
    public function search_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/search?q=html');
        $response->assertStatus(401);
    }
}