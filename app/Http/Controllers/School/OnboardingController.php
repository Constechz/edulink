<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\PageRevision;
use App\Models\Role;
use App\Models\School;
use App\Models\ScoringConfiguration;
use App\Models\Term;
use App\Models\User;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    /**
     * Show onboarding form.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Retrieve associated school via the tenant identifier
        $school = null;
        if ($user) {
            $school = School::find($user->school_id);
        }
        
        if (!$school) {
            $school = School::first(); // Fallback for setup/testing
        }

        if (!$school) {
            return redirect()->route('login')->withErrors(['school' => 'No associated school found.']);
        }

        if ($school->onboarding_completed) {
            return redirect('/dashboard');
        }

        return view('school.onboarding', compact('school'));
    }

    /**
     * Complete the onboarding steps.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $school = null;
        if ($user) {
            $school = School::find($user->school_id);
        }
        
        if (!$school) {
            $school = School::first(); // Fallback
        }

        if (!$school) {
            return response()->json(['error' => 'School not found.'], 404);
        }

        $step = $request->input('step');

        try {
            return DB::transaction(function () use ($request, $school, $step) {
                switch ($step) {
                    case 1:
                        // Step 1: Branding parameters (colors, font, logo)
                        $request->validate([
                            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                            'font_family' => 'required|string|in:Outfit,Inter,Roboto',
                            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                            'headteacher_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                        ]);

                        $logoPath = $school->logo;
                        if ($request->hasFile('logo')) {
                            $logoPath = $request->file('logo')->store('schools/logos', 'public');
                        }

                        $settings = $school->settings ?? [];
                        if ($request->hasFile('headteacher_signature')) {
                            $settings['headteacher_signature'] = $request->file('headteacher_signature')->store('schools/signatures', 'public');
                        }

                        $school->update([
                            'logo' => $logoPath,
                            'settings' => $settings,
                            'branding' => [
                                'primary_color' => $request->primary_color,
                                'accent_color' => $request->accent_color,
                                'font_family' => $request->font_family,
                                'logo' => $logoPath ?: 'https://ui-avatars.com/api/?name=' . urlencode($school->name) . '&background=003366&color=fff',
                            ]
                        ]);
                        return response()->json(['success' => true, 'message' => 'Branding parameters saved successfully.']);

                    case 2:
                        // Step 2: Academic calendar setup (Year and Terms)
                        $request->validate([
                            'academic_year' => 'required|string',
                            'term_name' => 'required|string',
                            'start_date' => 'required|date',
                            'end_date' => 'required|date|after:start_date',
                        ]);

                        $year = AcademicYear::updateOrCreate([
                            'school_id' => $school->id,
                            'name' => $request->academic_year,
                        ], [
                            'start_date' => $request->start_date,
                            'end_date' => $request->end_date,
                            'is_current' => true,
                        ]);

                        Term::updateOrCreate([
                            'school_id' => $school->id,
                            'academic_year_id' => $year->id,
                            'name' => $request->term_name,
                        ], [
                            'start_date' => $request->start_date,
                            'end_date' => $request->end_date,
                            'is_current' => true,
                        ]);

                        return response()->json(['success' => true, 'message' => 'Academic calendar and terms initialized.']);

                    case 3:
                        // Step 3: Primary administrator user creation
                        $request->validate([
                            'admin_name' => 'required|string|max:255',
                            'admin_email' => 'required|email|max:255',
                            'admin_password' => 'required|string|min:8',
                        ]);

                        $role = Role::where('slug', 'school-admin')->first();

                        User::updateOrCreate([
                            'school_id' => $school->id,
                            'email' => $request->admin_email,
                        ], [
                            'name' => $request->admin_name,
                            'password' => Hash::make($request->admin_password),
                            'role_id' => $role ? $role->id : null,
                            'is_active' => true,
                        ]);

                        return response()->json(['success' => true, 'message' => 'Administrator user configured successfully.']);

                    case 4:
                        // Step 4: Default score configurations (GES weight definitions)
                        $request->validate([
                            'class_weight' => 'required|integer|min:0|max:100',
                            'exam_weight' => 'required|integer|min:0|max:100',
                        ]);

                        if (($request->class_weight + $request->exam_weight) !== 100) {
                            return response()->json(['error' => 'The sum of Class Weight and Exam Weight must equal 100.'], 422);
                        }

                        $year = AcademicYear::where('school_id', $school->id)->where('is_current', true)->first();

                        ScoringConfiguration::updateOrCreate([
                            'school_id' => $school->id,
                            'academic_year_id' => $year ? $year->id : null,
                            'is_default' => true,
                        ], [
                            'name' => 'GES Standard Configuration',
                            'class_score_max' => 100,
                            'class_score_weight' => $request->class_weight,
                            'exam_score_max' => 100,
                            'exam_score_weight' => $request->exam_weight,
                            'grand_total' => 100,
                            'rounding_method' => 'ROUND',
                            'decimal_places' => 2,
                            'is_active' => true,
                        ]);

                        return response()->json(['success' => true, 'message' => 'GES scoring weights configuration saved.']);

                    case 5:
                        // Step 5: GrapesJS homepage initialization & finalize
                        $userId = $request->user() ? $request->user()->id : null;

                        $homepage = WebsitePage::updateOrCreate([
                            'school_id' => $school->id,
                            'slug' => 'home',
                        ], [
                            'title' => 'Home',
                            'meta_description' => 'Welcome to the official homepage of ' . $school->name,
                            'page_type' => 'custom',
                            'is_homepage' => true,
                            'is_published' => true,
                            'published_at' => now(),
                            'created_by' => $userId,
                        ]);

                        PageRevision::updateOrCreate([
                            'website_page_id' => $homepage->id,
                            'revision_number' => 1,
                        ], [
                            'html_content' => '
                                <div class="container py-5 text-center">
                                    <h1 class="display-3 fw-bold mb-4">Welcome to ' . e($school->name) . '</h1>
                                    <p class="lead mb-4">Empowering minds, shaping futures, and building leaders of tomorrow with quality education.</p>
                                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                        <button type="button" class="btn btn-primary btn-lg px-4 gap-3">Admissions</button>
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4">Contact Us</button>
                                    </div>
                                </div>
                            ',
                            'css_content' => '.display-3 { color: ' . ($school->branding['primary_color'] ?? '#003366') . '; }',
                            'is_current_draft' => true,
                            'is_published' => true,
                            'published_at' => now(),
                            'created_by' => $userId,
                            'published_by' => $userId,
                        ]);

                        // Finalize the onboarding
                        $school->update(['onboarding_completed' => true]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Onboarding complete! Redirecting...',
                            'redirect' => '/dashboard'
                        ]);

                    default:
                        return response()->json(['error' => 'Invalid step.'], 422);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
