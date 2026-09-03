<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->words(3, true),
            'description' => fake()->sentence(),
            'created_by'  => User::factory(),
        ];
    }
}