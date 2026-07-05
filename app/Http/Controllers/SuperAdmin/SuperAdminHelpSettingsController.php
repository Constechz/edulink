<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SuperAdminHelpSettingsController extends Controller
{
    /**
     * Display the customizable help settings view.
     */
    public function index()
    {
        $manuals = json_decode(SystemSetting::getVal('help_role_manuals', '[]'), true);
        $quickRefSba = json_decode(SystemSetting::getVal('help_quick_ref_sba', '[]'), true);
        $roadmap = json_decode(SystemSetting::getVal('help_roadmap', '[]'), true);
        $trainingVideos = json_decode(SystemSetting::getVal('help_training_videos', '[]'), true);

        return view('super-admin.help-settings.index', compact('manuals', 'quickRefSba', 'roadmap', 'trainingVideos'));
    }

    /**
     * Update the customizable help settings.
     */
    public function update(Request $request)
    {
        // 1. Manuals
        $manuals = [];
        if ($request->has('manuals')) {
            foreach ($request->manuals as $key => $data) {
                $manuals[] = [
                    'key' => $key,
                    'title' => $data['title'] ?? '',
                    'icon' => $data['icon'] ?? 'bi-people',
                    'description' => $data['description'] ?? '',
                    'items' => array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $data['items'] ?? ''))))),
                    'is_super_only' => isset($data['is_super_only']),
                ];
            }
        }
        SystemSetting::setVal('help_role_manuals', json_encode($manuals, JSON_PRETTY_PRINT));

        // 2. SBA Quick Reference
        $quickRefSba = [
            'formula_class' => $request->input('quick_ref.formula_class', ''),
            'formula_exam' => $request->input('quick_ref.formula_exam', ''),
            'example_text' => $request->input('quick_ref.example_text', ''),
        ];
        SystemSetting::setVal('help_quick_ref_sba', json_encode($quickRefSba, JSON_PRETTY_PRINT));

        // 3. Roadmap
        $roadmap = [];
        if ($request->has('roadmap')) {
            foreach ($request->roadmap as $milestone) {
                if (!empty($milestone['title'])) {
                    $roadmap[] = [
                        'title' => $milestone['title'],
                        'color' => $milestone['color'] ?? 'primary',
                        'description' => $milestone['description'] ?? '',
                    ];
                }
            }
        }
        SystemSetting::setVal('help_roadmap', json_encode($roadmap, JSON_PRETTY_PRINT));

        // 4. Training Videos
        $trainingVideos = [];
        if ($request->has('videos')) {
            foreach ($request->videos as $video) {
                if (!empty($video['title'])) {
                    $trainingVideos[] = [
                        'title' => $video['title'],
                        'description' => $video['description'] ?? '',
                        'youtube_url' => $video['youtube_url'] ?? '',
                    ];
                }
            }
        }
        SystemSetting::setVal('help_training_videos', json_encode($trainingVideos, JSON_PRETTY_PRINT));

        return redirect()->route('super-admin.help-settings.index')->with('success', 'Help Center configuration updated successfully.');
    }
}
