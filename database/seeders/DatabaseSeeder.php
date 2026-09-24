<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters — foreign keys enforced
        $this->call([
            PlansSeeder::class,           // 1. Plans first (no dependencies)
            DefaultOrganizationSeeder::class, // 2. Org needs Plans + admin User
        ]);
    }
}