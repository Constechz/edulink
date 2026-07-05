<?php

namespace App\Services\Subscription;

use App\Models\School;
use App\Models\Campus;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class SubscriptionLimitService
{
    /**
     * Check if a school has reached its student limit.
     *
     * @throws ValidationException
     */
    public function checkStudentLimit(int $schoolId): void
    {
        $school = School::with('plan')->findOrFail($schoolId);
        $plan = $school->plan;

        if (!$plan) {
            return; // No plan associated, bypass limit check
        }

        $currentCount = Student::where('school_id', $schoolId)->count();

        if ($currentCount >= $plan->max_students) {
            throw ValidationException::withMessages([
                'limit' => "Student limit reached. Your current plan '{$plan->name}' only allows a maximum of {$plan->max_students} students."
            ]);
        }
    }

    /**
     * Check if a school has reached its staff limit.
     *
     * @throws ValidationException
     */
    public function checkStaffLimit(int $schoolId): void
    {
        $school = School::with('plan')->findOrFail($schoolId);
        $plan = $school->plan;

        if (!$plan) {
            return;
        }

        $currentCount = Staff::where('school_id', $schoolId)->count();

        if ($currentCount >= $plan->max_staff) {
            throw ValidationException::withMessages([
                'limit' => "Staff limit reached. Your current plan '{$plan->name}' only allows a maximum of {$plan->max_staff} staff members."
            ]);
        }
    }

    /**
     * Check if a school has reached its campus limit.
     *
     * @throws ValidationException
     */
    public function checkCampusLimit(int $schoolId): void
    {
        $school = School::with('plan')->findOrFail($schoolId);
        $plan = $school->plan;

        if (!$plan) {
            return;
        }

        $currentCount = Campus::where('school_id', $schoolId)->count();

        if ($currentCount >= $plan->max_campuses) {
            throw ValidationException::withMessages([
                'limit' => "Campus limit reached. Your current plan '{$plan->name}' only allows a maximum of {$plan->max_campuses} campuses."
            ]);
        }
    }
}
