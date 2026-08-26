<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'username'          => fake()->unique()->userName(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => UserRole::Learner->value,
            'remember_token'    => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => ['role' => UserRole::Admin->value]);
    }

    public function learner(): static
    {
        return $this->state(fn() => ['role' => UserRole::Learner->value]);
    }

    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }
}