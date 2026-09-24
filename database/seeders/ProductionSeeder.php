<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Production seed — runs ONCE on first deployment.
     * Only creates the minimum required data:
     *   1. Plans (Starter / Professional / Enterprise)
     *   2. Admin user
     *   3. Default organization
     *
     * NEVER run db:seed without --class=ProductionSeeder on production.
     * NEVER run db:seed (DatabaseSeeder) on production — it may include test data.
     */
    public function run(): void
    {
        $this->command->info('Seeding plans...');
        $this->call(PlansSeeder::class);

        $this->command->info('Creating admin user...');
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@codemaster.com')],
            [
                'name'              => env('ADMIN_NAME', 'Code Master Admin'),
                'username'          => 'admin',
                'password'          => Hash::make(env('ADMIN_PASSWORD', 'CHANGE_ME_ON_FIRST_LOGIN')),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Creating default organization...');
        $plan = Plan::where('slug', 'enterprise')->first();

        $org = Organization::firstOrCreate(
            ['slug' => env('DEFAULT_ORG_SLUG', 'default')],
            [
                'name'      => env('APP_NAME', 'Code Master Academy'),
                'subdomain' => env('DEFAULT_ORG_SUBDOMAIN', 'app'),
                'owner_id'  => $admin->id,
                'plan_id'   => $plan?->id,
                'is_active' => true,
            ]
        );

        // Add admin as owner member
        if (!$org->users()->where('user_id', $admin->id)->exists()) {
            $org->users()->attach($admin->id, [
                'role'      => 'owner',
                'status'    => 'active',
                'joined_at' => now(),
            ]);
        }

        $this->command->info('');
        $this->command->info('Production seed complete:');
        $this->command->info("  Admin:  {$admin->email}");
        $this->command->info("  Org:    {$org->name} (slug: {$org->slug})");
        $this->command->info("  Plan:   {$plan?->name}");
        $this->command->warn('  IMPORTANT: Change admin password immediately after first login!');
    }
}