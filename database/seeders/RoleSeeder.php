<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Permissions
        $permissionsList = [
            // Platform permissions (Super Admin only)
            ['name' => 'Manage Schools', 'slug' => 'manage-schools', 'module' => 'Platform', 'description' => 'Create and modify school tenants'],
            ['name' => 'Manage Plans', 'slug' => 'manage-plans', 'module' => 'Platform', 'description' => 'Manage subscription plans'],

            // School Admin permissions
            ['name' => 'Manage Onboarding', 'slug' => 'manage-onboarding', 'module' => 'Administration', 'description' => 'Complete onboarding checklist'],
            ['name' => 'Manage School Settings', 'slug' => 'manage-settings', 'module' => 'Administration', 'description' => 'Configure school-specific settings'],
            ['name' => 'Manage Campuses', 'slug' => 'manage-campuses', 'module' => 'Administration', 'description' => 'Create and edit campuses'],
            ['name' => 'Manage Staff', 'slug' => 'manage-staff', 'module' => 'HR', 'description' => 'Manage school staff and roles'],

            // Academic structure
            ['name' => 'Manage Academics', 'slug' => 'manage-academics', 'module' => 'Academics', 'description' => 'Configure terms, classes, streams, subjects'],
            ['name' => 'Manage Enrollments', 'slug' => 'manage-enrollments', 'module' => 'Academics', 'description' => 'Enroll and class-assign students'],

            // Scoring Engine
            ['name' => 'Configure Scoring', 'slug' => 'configure-scoring', 'module' => 'Scoring', 'description' => 'Define class score max, weights, and components'],
            ['name' => 'Enter Scores', 'slug' => 'enter-scores', 'module' => 'Scoring', 'description' => 'Enter student class and exam marks'],
            ['name' => 'Verify Scores', 'slug' => 'verify-scores', 'module' => 'Scoring', 'description' => 'Verify teacher scores (HOD role)'],
            ['name' => 'Approve Scores', 'slug' => 'approve-scores', 'module' => 'Scoring', 'description' => 'Approve scores and broadsheets (Headteacher role)'],
            ['name' => 'Publish Report Cards', 'slug' => 'publish-reports', 'module' => 'Scoring', 'description' => 'Publish term reports to portals'],

            // Finance
            ['name' => 'Manage Fees', 'slug' => 'manage-fees', 'module' => 'Finance', 'description' => 'Define fees and generate invoices'],
            ['name' => 'Collect Payments', 'slug' => 'collect-payments', 'module' => 'Finance', 'description' => 'Record student fee payments'],
            ['name' => 'View Accounts', 'slug' => 'view-accounts', 'module' => 'Finance', 'description' => 'Access accounting ledger and reports'],

            // Website Builder
            ['name' => 'Manage Website', 'slug' => 'manage-website', 'module' => 'CMS', 'description' => 'Create and customize pages in drag-and-drop builder'],

            // Safeguarding
            ['name' => 'Manage Safeguarding', 'slug' => 'manage-safeguarding', 'module' => 'Safeguarding', 'description' => 'Access highly restricted student safeguarding records'],
        ];

        $permissions = [];
        foreach ($permissionsList as $perm) {
            $permissions[$perm['slug']] = Permission::updateOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        // 2. Create Global Platform Roles (school_id = null)
        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'super-admin', 'school_id' => null],
            [
                'name' => 'Super Administrator',
                'description' => 'Platform Owner with full unrestricted access',
                'is_system' => true,
            ]
        );
        // Super Admin gets all permissions
        $superAdminRole->permissions()->sync(Permission::all());

        // 3. Create Template School System Roles (to be generated or assigned upon school creation)
        // Note: For multi-tenant, system roles can be pre-created with school_id = NULL
        // and inherited/scoped, or created per tenant.
        // Let's create system-wide template roles that schools inherit, marked with school_id = null.
        
        $schoolAdminRole = Role::updateOrCreate(
            ['slug' => 'school-admin', 'school_id' => null],
            [
                'name' => 'School Administrator',
                'description' => 'School Owner/Administrator with management rights',
                'is_system' => true,
            ]
        );
        // School Admin gets everything except platform management
        $schoolAdminPermissions = Permission::whereNotIn('module', ['Platform'])->get();
        $schoolAdminRole->permissions()->sync($schoolAdminPermissions);

        $headTeacherRole = Role::updateOrCreate(
            ['slug' => 'headteacher', 'school_id' => null],
            [
                'name' => 'Headteacher',
                'description' => 'Academic Principal/Headteacher',
                'is_system' => true,
            ]
        );
        // Headteacher gets Academics, Scoring approval/publishing, and Safeguarding
        $headTeacherPermissions = Permission::whereIn('module', ['Academics', 'Scoring', 'Safeguarding'])
            ->orWhereIn('slug', ['manage-staff', 'view-accounts'])->get();
        $headTeacherRole->permissions()->sync($headTeacherPermissions);

        $hodRole = Role::updateOrCreate(
            ['slug' => 'hod', 'school_id' => null],
            [
                'name' => 'Head of Department',
                'description' => 'Head of Academic Department',
                'is_system' => true,
            ]
        );
        // HOD gets Academics, Scoring entry & verification
        $hodPermissions = Permission::whereIn('slug', ['enter-scores', 'verify-scores', 'manage-academics', 'manage-enrollments'])->get();
        $hodRole->permissions()->sync($hodPermissions);

        $classTeacherRole = Role::updateOrCreate(
            ['slug' => 'class-teacher', 'school_id' => null],
            [
                'name' => 'Class Teacher',
                'description' => 'Classroom Teacher with class management and score entry rights',
                'is_system' => true,
            ]
        );
        // Class Teacher gets enter-scores and manage-enrollments
        $classTeacherPermissions = Permission::whereIn('slug', ['enter-scores', 'manage-enrollments'])->get();
        $classTeacherRole->permissions()->sync($classTeacherPermissions);

        $subjectTeacherRole = Role::updateOrCreate(
            ['slug' => 'subject-teacher', 'school_id' => null],
            [
                'name' => 'Subject Teacher',
                'description' => 'Subject Teacher with score entry rights',
                'is_system' => true,
            ]
        );
        // Subject Teacher gets enter-scores only
        $subjectTeacherPermissions = Permission::whereIn('slug', ['enter-scores'])->get();
        $subjectTeacherRole->permissions()->sync($subjectTeacherPermissions);

        $studentRole = Role::updateOrCreate(
            ['slug' => 'student', 'school_id' => null],
            [
                'name' => 'Student',
                'description' => 'School Student',
                'is_system' => true,
            ]
        );
        // Students get no admin permissions by default

        $parentRole = Role::updateOrCreate(
            ['slug' => 'parent', 'school_id' => null],
            [
                'name' => 'Parent / Guardian',
                'description' => 'Student Guardian',
                'is_system' => true,
            ]
        );
        // Parents get no admin permissions by default
    }
}
