<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ReportCardThemeController extends Controller
{
    /**
     * Display report card theme configurations index.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        // Fetch classes authorized for this user
        if ($user->role && $user->role->slug === 'class-teacher') {
            $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
            $classes = SchoolClass::where('school_id', $schoolId)
                ->where(function($q) use ($user, $assignedClassIdsFromStreams) {
                    $q->where('class_teacher_id', $user->id)
                      ->orWhereIn('id', $assignedClassIdsFromStreams);
                })
                ->get();
        } else {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
        }

        $themes = $this->getAvailableThemes();

        return view('school.reports.themes', compact('classes', 'themes'));
    }

    /**
     * Update dynamic themes configuration in bulk.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $request->validate([
            'class_themes' => 'required|array',
            'class_themes.*' => 'required|string|in:classic,royal,vibrant,minimalist',
        ]);

        foreach ($request->class_themes as $classId => $themeKey) {
            $class = SchoolClass::where('school_id', $schoolId)->findOrFail($classId);

            // Double check authorization for class teachers
            if ($user->role && $user->role->slug === 'class-teacher') {
                $assignedClassIdsFromStreams = \App\Models\Stream::where('class_teacher_id', $user->id)->pluck('class_id')->toArray();
                $isAuthorized = ($class->class_teacher_id === $user->id) || in_array($class->id, $assignedClassIdsFromStreams);
                if (!$isAuthorized) {
                    abort(403, 'Unauthorized class access.');
                }
            }

            $class->update(['report_card_theme' => $themeKey]);
        }

        return redirect()->route('school.reports.index')->with('success', 'Report card themes updated successfully.');
    }

    /**
     * Get list of theme presets.
     */
    private function getAvailableThemes(): array
    {
        return [
            'classic' => [
                'name' => 'Classic GES Blue',
                'description' => 'Official traditional Ghana Education Service layout with clean borders and navy headers.',
                'primary' => '#002244',
                'secondary' => '#f1f5f9',
                'border' => '#000000',
                'font' => 'Helvetica',
            ],
            'royal' => [
                'name' => 'Royal Gold & Maroon',
                'description' => 'Elite academy template utilizing serif typography, gold accents, and rich maroon headers.',
                'primary' => '#660000',
                'secondary' => '#fdfbf7',
                'border' => '#D4AF37',
                'font' => 'Georgia',
            ],
            'vibrant' => [
                'name' => 'Vibrant Playroom',
                'description' => 'A colorful teal/yellow design with rounded components for preschool & primary kids.',
                'primary' => '#0f766e',
                'secondary' => '#fefce8',
                'border' => '#0d9488',
                'font' => 'Outfit',
            ],
            'minimalist' => [
                'name' => 'Sleek Minimalist',
                'description' => 'Modern professional high-school layout highlighting negative space and charcoal borders.',
                'primary' => '#1e293b',
                'secondary' => '#fafafa',
                'border' => '#cbd5e1',
                'font' => 'Inter',
            ]
        ];
    }
}
