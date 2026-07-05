<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Disable tenant scope check for seeder since we don't have a bound school context for Super Admin creation
        $superAdminRole = Role::withoutGlobalScopes()->where('slug', 'super-admin')->first();

        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'admin@edulink.com'],
            [
                'name' => 'EduLink Super Admin',
                'phone' => '+233240000000',
                'password' => Hash::make('password123'),
                'role_id' => $superAdminRole ? $superAdminRole->id : null,
                'school_id' => null, // null represents system-wide platform user
                'campus_id' => null,
                'is_active' => true,
            ]
        );

        // Seed default help manuals including Super Admin guide
        $manuals = [
            [
                'key' => 'super_admin',
                'title' => 'Super Administrator Portal Guide',
                'icon' => 'bi-shield-lock-fill',
                'description' => 'Enterprise-level SaaS subscription provisioning, server resource gating, and paywall configurations.',
                'items' => [
                    'Navigate to Super Admin -> Subscription Plans to create and edit SaaS tiers.',
                    'Toggle allowed features for each plan (e.g. Finance, LMS, Hostels, Transport) to restrict or allow access.',
                    'Verify route-level middleware gatekeeping: system blocks unchecked modules with a 403 error.',
                    'Utilize Settings -> Paywalls to define which modules are globally restricted to paid accounts.',
                    'Monitor server diagnostics, active tenant list, and platform metrics from the main Super Admin Dashboard.'
                ],
                'is_super_only' => true
            ],
            [
                'key' => 'school_admin',
                'title' => 'School Administrator Guide',
                'icon' => 'bi-sliders',
                'description' => 'Onboarding settings, academic year setup, and student roster promotions policy configuration.',
                'items' => [
                    'Complete the Onboarding Checklist: Setup school profile, campuses, academic years, and terms.',
                    'Register staff accounts and assign custom roles (e.g., Class Teacher, Accountant).',
                    'Allocate subject teachers to class streams to enable class score entry sheets.',
                    'Configure continuous assessment (SBA) templates and raw scaling weights.',
                    'Set promotion thresholds and weights under Student Promotions -> Promotion Rules.'
                ],
                'is_super_only' => false
            ],
            [
                'key' => 'class_teacher',
                'title' => 'Class Teacher / Instructor Guide',
                'icon' => 'bi-journal-check',
                'description' => 'Classroom attendance logs, student score entry spreadsheets, and terminal reports.',
                'items' => [
                    'Access student lists and mark classroom daily attendance records.',
                    'Enter class test and terminal exam grades in the Score Entry Spreadsheet.',
                    'Verify and submit scores to HODs / Headteachers for final review and approval.',
                    'Write term remarks and check student promotion eligibility details.'
                ],
                'is_super_only' => false
            ]
        ];

        \App\Models\SystemSetting::setVal('help_role_manuals', json_encode($manuals, JSON_PRETTY_PRINT));

        if (!\App\Models\SystemSetting::getVal('help_quick_ref_sba')) {
            \App\Models\SystemSetting::setVal('help_quick_ref_sba', json_encode([
                'formula_class' => 'SBA Class Score = (Raw Total / Max Mark) * Class Weight (e.g., 30%)',
                'formula_exam' => 'SBA Exam Score = (Raw Exam / 100) * Exam Weight (e.g., 70%)',
                'example_text' => 'For a student scoring 45/50 in Class Assessment (30% weight) and 80/100 in Term Exam (70% weight): Cumulative Total = (45/50 * 30) + (80/100 * 70) = 27 + 56 = 83% (Excellent Grade A1).'
            ], JSON_PRETTY_PRINT));
        }

        if (!\App\Models\SystemSetting::getVal('help_roadmap')) {
            \App\Models\SystemSetting::setVal('help_roadmap', json_encode([
                ['title' => 'Phase 1 - Onboarding & Academics', 'color' => 'success', 'description' => 'Setup school parameters, class lists, student profiles, and subject maps.'],
                ['title' => 'Phase 2 - Continuous Assessment & Finance', 'color' => 'primary', 'description' => 'Scale and verify SBA marks, automate invoicing, log payments, and generate term report cards.'],
                ['title' => 'Phase 3 - SaaS Scale & Deployments', 'color' => 'warning', 'description' => 'Custom domain integrations, multi-tenant billing, automated database backups, and health dashboarding.']
            ], JSON_PRETTY_PRINT));
        }

        if (!\App\Models\SystemSetting::getVal('help_training_videos')) {
            \App\Models\SystemSetting::setVal('help_training_videos', json_encode([
                ['title' => '1. Platform Overview (10 mins)', 'description' => 'An introductory guide covering dashboard widgets, quick navigations, and profile settings.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                ['title' => '2. Managing Student Promotions (15 mins)', 'description' => 'A comprehensive walkthrough demonstrating promotion rules setup, candidate filters, and bulk rollover executions.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
            ], JSON_PRETTY_PRINT));
        }
    }
}
