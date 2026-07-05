<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'max_students' => 50,
                'max_staff' => 10,
                'max_campuses' => 1,
                'features' => ['dashboard', 'academics', 'students'],
                'sms_credits_monthly' => 100,
                'storage_gb' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Standard',
                'price_monthly' => 200.00,
                'price_yearly' => 2000.00,
                'max_students' => 500,
                'max_staff' => 50,
                'max_campuses' => 2,
                'features' => ['dashboard', 'academics', 'students', 'finance', 'website_builder', 'attendance', 'sms'],
                'sms_credits_monthly' => 1000,
                'storage_gb' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'price_monthly' => 500.00,
                'price_yearly' => 5000.00,
                'max_students' => 2000,
                'max_staff' => 150,
                'max_campuses' => 5,
                'features' => ['dashboard', 'academics', 'students', 'finance', 'website_builder', 'lms', 'portals', 'attendance', 'sms', 'custom_domain'],
                'sms_credits_monthly' => 5000,
                'storage_gb' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'price_monthly' => 1500.00,
                'price_yearly' => 15000.00,
                'max_students' => -1,
                'max_staff' => -1,
                'max_campuses' => -1,
                'features' => ['dashboard', 'academics', 'students', 'finance', 'website_builder', 'lms', 'portals', 'attendance', 'sms', 'custom_domain', 'api', 'ai_analytics', 'safeguarding', 'monitoring'],
                'sms_credits_monthly' => 20000,
                'storage_gb' => 500,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
