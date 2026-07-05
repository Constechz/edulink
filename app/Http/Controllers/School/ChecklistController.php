<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\School;
use App\Models\ScoringConfiguration;
use App\Models\Staff;
use App\Models\User;
use App\Models\WebsitePage;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    /**
     * Show interactive onboarding checklist.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $school = School::findOrFail($user->school_id);

        // 1. Branding check
        $brandingDone = is_array($school->branding) && isset($school->branding['primary_color']);

        // 2. Calendar check
        $calendarDone = AcademicYear::where('school_id', $school->id)->exists();

        // 3. Admin user check
        $adminDone = User::where('school_id', $school->id)
            ->whereHas('role', function ($q) {
                $q->where('slug', 'school-admin');
            })->exists();

        // 4. Grading config check
        $gradingDone = ScoringConfiguration::where('school_id', $school->id)->exists();

        // 5. Homepage check
        $homepageDone = WebsitePage::where('school_id', $school->id)->where('is_homepage', true)->exists();

        // 6. Campus check
        $campusDone = Campus::where('school_id', $school->id)->exists();

        // 7. Staff check
        $staffDone = Staff::where('school_id', $school->id)->exists();

        $checklist = [
            [
                'title' => 'Configure School Branding',
                'desc' => 'Define branding primary and accent colors used across student/parent portals.',
                'status' => $brandingDone,
                'url' => '/school/settings#profile',
            ],
            [
                'title' => 'Set Up Academic Calendar',
                'desc' => 'Create terms and starting dates for the active academic year.',
                'status' => $calendarDone,
                'url' => '/school/settings#academic',
            ],
            [
                'title' => 'Register Primary Administrator',
                'desc' => 'Initialize the school principal or administrator credentials.',
                'status' => $adminDone,
                'url' => '/school/staff',
            ],
            [
                'title' => 'Configure Evaluation Rules',
                'desc' => 'Set continuous assessment (SBA) and terminal examination weights.',
                'status' => '/school/onboarding', // Quick re-run onboard if needed, or settings
                'status_val' => $gradingDone,
                'url' => '/school/settings',
            ],
            [
                'title' => 'Register First Campus Branch',
                'desc' => 'Setup regional branch campuses for multi-campus structures.',
                'status' => $campusDone,
                'url' => '/school/campuses',
            ],
            [
                'title' => 'Enroll Staff Members',
                'desc' => 'Map teachers, finance officers, and registrars to permissions.',
                'status' => $staffDone,
                'url' => '/school/staff',
            ],
            [
                'title' => 'Launch School Website Homepage',
                'desc' => 'Seeded homepage details loaded inside website page repository.',
                'status' => $homepageDone,
                'url' => '/school/settings',
            ],
        ];

        // Calculate progress
        $totalItems = count($checklist);
        $completedItems = 0;
        foreach ($checklist as $item) {
            $status = isset($item['status_val']) ? $item['status_val'] : $item['status'];
            if ($status) {
                $completedItems++;
            }
        }

        $progressPercent = $totalItems > 0 ? (int)(($completedItems / $totalItems) * 100) : 0;

        return view('school.checklist', compact('school', 'checklist', 'progressPercent', 'completedItems', 'totalItems'));
    }
}
