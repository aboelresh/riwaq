<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'  => 'Starter',
                'slug'  => 'starter',
                'price_monthly'  => 29,
                'price_yearly'   => 290,
                'max_students'   => 50,
                'max_instructors'=> 2,
                'max_courses'    => 5,
                'max_tracks'     => 2,
                'max_storage_gb' => 5,
                'max_ai_calls_per_month' => 200,
                'allow_custom_domain' => false,
                'allow_white_label'   => false,
                'allow_api_access'    => false,
            ],
            [
                'name'  => 'Professional',
                'slug'  => 'professional',
                'price_monthly'  => 99,
                'price_yearly'   => 990,
                'max_students'   => 500,
                'max_instructors'=> 10,
                'max_courses'    => 50,
                'max_tracks'     => 20,
                'max_storage_gb' => 50,
                'max_ai_calls_per_month' => 2000,
                'allow_custom_domain' => true,
                'allow_white_label'   => false,
                'allow_api_access'    => false,
            ],
            [
                'name'  => 'Enterprise',
                'slug'  => 'enterprise',
                'price_monthly'  => 299,
                'price_yearly'   => 2990,
                'max_students'   => 9999,
                'max_instructors'=> 9999,
                'max_courses'    => 9999,
                'max_tracks'     => 9999,
                'max_storage_gb' => 500,
                'max_ai_calls_per_month' => 99999,
                'allow_custom_domain' => true,
                'allow_white_label'   => true,
                'allow_api_access'    => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}