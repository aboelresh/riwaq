<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $plan  = Plan::where('slug', 'enterprise')->first();

        Organization::firstOrCreate(
            ['slug' => 'default'],
            [
                'name'      => 'Riwaq Academy',
                'subdomain' => 'app',
                'owner_id'  => $admin->id,
                'plan_id'   => $plan?->id,
                'is_active' => true,
            ]
        );

        $org = Organization::where('slug', 'default')->first();

        // Add admin as owner member
        if (!$org->users()->where('user_id', $admin->id)->exists()) {
            $org->users()->attach($admin->id, [
                'role'      => 'owner',
                'status'    => 'active',
                'joined_at' => now(),
            ]);
        }
    }
}