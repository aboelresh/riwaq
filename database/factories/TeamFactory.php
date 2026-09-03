<?php

namespace Database\Factories;

use App\Enums\ProjectType;
use App\Enums\TeamType;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => fake()->words(2, true) . ' Team',
            'description'  => fake()->sentence(),
            'type'         => TeamType::Project->value,
            'project_type' => ProjectType::Web->value,
            'max_members'  => 5,
            'code'         => strtoupper(fake()->unique()->lexify('????????')),
            'created_by'   => User::factory(),
        ];
    }
}