<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $query = $request->input('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'students' => [],
                'staff' => [],
                'subjects' => [],
            ]);
        }

        // 1. Search Students
        $students = Student::where('school_id', $schoolId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('student_id_number', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'title' => "{$s->first_name} {$s->last_name}",
                    'subtitle' => "Student ({$s->student_id_number})",
                    'type' => 'student',
                ];
            });

        // 2. Search Staff
        $staff = Staff::where('school_id', $schoolId)
            ->where(function ($q) use ($query) {
                $q->where('staff_number', 'like', "%{$query}%")
                  ->orWhere('designation', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($uq) use ($query) {
                      $uq->where('name', 'like', "%{$query}%");
                  });
            })
            ->with('user')
            ->limit(10)
            ->get()
            ->map(function ($st) {
                return [
                    'id' => $st->id,
                    'title' => $st->user->name ?? 'Staff Name',
                    'subtitle' => "Staff - {$st->designation} ({$st->staff_number})",
                    'type' => 'staff',
                ];
            });

        // 3. Search Subjects
        $subjects = Subject::where('school_id', $schoolId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'title' => $sub->name,
                    'subtitle' => "Subject ({$sub->code})",
                    'type' => 'subject',
                ];
            });

        return response()->json([
            'success' => true,
            'results' => [
                'students' => $students,
                'staff' => $staff,
                'subjects' => $subjects,
            ]
        ]);
    }
}
