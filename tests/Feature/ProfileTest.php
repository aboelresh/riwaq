<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name'              => 'Test User',
            'bio'               => null,
            'goals'             => null,
            'profile_completed' => false,
        ]);
    }

    #[Test]
    public function profile_does_not_expose_sensitive_fields_bug007(): void
    {
        $response = $this->getJson('/api/v1/profile', $this->authHeaders($this->user));

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertArrayNotHasKey('password',                   $data, 'password must be hidden');
        $this->assertArrayNotHasKey('verification_code',          $data, 'Bug007: verification_code must be hidden');
        $this->assertArrayNotHasKey('verification_code_expires_at',$data);
        $this->assertArrayNotHasKey('remember_token',             $data);
    }

    #[Test]
    public function update_profile_with_invalid_name_format_returns_422_bug017(): void
    {
        $response = $this->postJson('/api/v1/profile/update',
            ['name' => 'Ahmed123!@#'],
            $this->authHeaders($this->user)
        );

        $response->assertStatus(422)
                 ->assertJsonValidationErrorFor('name');

        $this->assertStringContainsString('letters', $response->json('errors.name.0'));
    }

    #[Test]
    public function update_profile_empty_body_does_not_set_profile_completed_bug008(): void
    {
        $response = $this->postJson('/api/v1/profile/update',
            [],
            $this->authHeaders($this->user)
        );

        $response->assertStatus(200);

        // Bug 008 fix: empty body must NOT mark profile as completed
        $this->assertFalse($response->json('data.profile_completed'));
        $this->assertDatabaseHas('users', [
            'id'                => $this->user->id,
            'profile_completed' => false,
        ]);
    }

    #[Test]
    public function update_profile_with_all_fields_marks_completed(): void
    {
        $response = $this->postJson('/api/v1/profile/update', [
            'name'  => 'Ahmed Complete',
            'bio'   => 'Developer',
            'goals' => 'Ship to production',
        ], $this->authHeaders($this->user));

        $response->assertStatus(200)
                 ->assertJsonPath('data.profile_completed', true);
    }

    #[Test]
    public function update_profile_with_valid_name_succeeds(): void
    {
        $response = $this->postJson('/api/v1/profile/update',
            ['name' => 'Ahmed Mohamed'],
            $this->authHeaders($this->user)
        );

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Ahmed Mohamed');
    }

    #[Test]
    public function profile_returns_user_resource_with_required_fields(): void
    {
        $response = $this->getJson('/api/v1/profile', $this->authHeaders($this->user));

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']]);
    }
}