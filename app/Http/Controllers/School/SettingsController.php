<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $school = School::with('plan')->findOrFail($user->school_id);
        
        // Auto-seed default grading scales if none exist for the school
        $gradingScalesCount = \App\Models\GradingScale::where('school_id', $school->id)->count();
        if ($gradingScalesCount === 0) {
            $school->seedDefaultGradingScales();
        }

        $academicYears = AcademicYear::where('school_id', $school->id)->get();
        $gradingScales = \App\Models\GradingScale::where('school_id', $school->id)->with('items')->get();

        return view('school.settings', compact('school', 'academicYears', 'gradingScales'));
    }

    /**
     * Update school general profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $school = School::findOrFail($user->school_id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'website_domain' => 'nullable|string|max:255',
            'custom_domain' => 'nullable|string|max:255',
            'attendance_cutoff_time' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'headteacher_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'student_id_prefix' => 'nullable|string|max:10',
            'student_id_format' => 'nullable|string|max:50',
            'student_id_next_sequence' => 'nullable|integer|min:1',
            'welcome_notification_channel' => 'nullable|string|in:email,sms,both',
        ]);

        $settings = $school->settings ?? [];

        if ($request->hasFile('logo')) {
            if ($school->logo && !\Illuminate\Support\Str::contains($school->logo, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($school->logo);
            }
            $school->logo = $request->file('logo')->store('schools/logos', 'public');
        }

        if ($request->hasFile('headteacher_signature')) {
            if (isset($settings['headteacher_signature'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['headteacher_signature']);
            }
            $settings['headteacher_signature'] = $request->file('headteacher_signature')->store('schools/signatures', 'public');
        }

        $cutoffTime = $request->input('attendance_cutoff_time');
        if ($cutoffTime) {
            $settings['attendance_cutoff_time'] = $cutoffTime . ':00'; // store as H:i:s
        } else {
            unset($settings['attendance_cutoff_time']);
        }

        // Student ID Generation settings
        $settings['student_id_prefix'] = $request->input('student_id_prefix', 'STD');
        $settings['student_id_format'] = $request->input('student_id_format', '{PREFIX}-{YEAR}-{SEQUENCE}');
        $settings['student_id_next_sequence'] = intval($request->input('student_id_next_sequence', 1));
        $settings['welcome_notification_channel'] = $request->input('welcome_notification_channel', 'both');

        $school->settings = $settings;

        unset($data['attendance_cutoff_time']);
        unset($data['logo']);
        unset($data['headteacher_signature']);
        unset($data['student_id_prefix']);
        unset($data['student_id_format']);
        unset($data['student_id_next_sequence']);
        unset($data['welcome_notification_channel']);

        $school->update($data);

        return redirect()->back()->with('success', 'School profile updated successfully.');
    }

    /**
     * Update email and SMS gateway configurations.
     */
    public function updateGateway(Request $request)
    {
        $user = $request->user();
        $school = School::findOrFail($user->school_id);

        $request->validate([
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string',
            'sms_provider' => 'nullable|string|in:arkesel,hubtel,log',
            'sms_api_key' => 'nullable|string',
            'sms_sender_id' => 'nullable|string|max:11',
        ]);

        $emailConfig = [
            'host' => $request->smtp_host,
            'port' => $request->smtp_port,
            'username' => $request->smtp_username,
            'password' => $request->smtp_password,
            'encryption' => $request->smtp_encryption,
            'from_address' => $request->smtp_from_address,
            'from_name' => $request->smtp_from_name,
        ];

        $smsConfig = [
            'provider' => $request->sms_provider,
            'api_key' => $request->sms_api_key,
            'sender_id' => $request->sms_sender_id,
        ];

        $school->update([
            'email_config' => $emailConfig,
            'sms_gateway_config' => $smsConfig,
        ]);

        return redirect()->back()->with('success', 'Gateway configurations updated successfully.');
    }

    /**
     * Toggle features based on the school subscription plan limits.
     */
    public function updateFeatures(Request $request)
    {
        $user = $request->user();
        $school = School::with('plan')->findOrFail($user->school_id);
        
        $plan = $school->plan;
        if (!$plan) {
            return redirect()->back()->withErrors(['plan' => 'No active subscription plan found.']);
        }

        $requestedModules = $request->input('modules', []);
        $allowedModules = $plan->features ?? [];
        
        // Filter out any requested modules that are not allowed by the school plan
        $enabledModules = [];
        foreach ($requestedModules as $module => $value) {
            if ($value == 1 && in_array($module, $allowedModules)) {
                $enabledModules[] = $module;
            }
        }

        // Maintain basic defaults that can't be toggled off if they are in the plan
        foreach (['dashboard', 'academics', 'students'] as $defaultModule) {
            if (in_array($defaultModule, $allowedModules) && !in_array($defaultModule, $enabledModules)) {
                $enabledModules[] = $defaultModule;
            }
        }

        // Save into settings JSON
        $settings = $school->settings ?? [];
        $settings['enabled_modules'] = $enabledModules;
        $school->update(['settings' => $settings]);

        return redirect()->back()->with('success', 'Feature flags configured successfully.');
    }

    /**
     * Update school payment gateway configurations.
     */
    public function updatePaymentSettings(Request $request)
    {
        $user = $request->user();
        $school = School::findOrFail($user->school_id);

        $request->validate([
            'paystack_public_key' => 'nullable|string',
            'paystack_secret_key' => 'nullable|string',
            'flutterwave_public_key' => 'nullable|string',
            'flutterwave_secret_key' => 'nullable|string',
        ]);

        $settings = $school->settings ?? [];
        $settings['payment_gateways'] = [
            'paystack' => [
                'public_key' => $request->paystack_public_key,
                'secret_key' => $request->paystack_secret_key,
                'enabled' => $request->has('paystack_enabled') ? '1' : '0',
            ],
            'flutterwave' => [
                'public_key' => $request->flutterwave_public_key,
                'secret_key' => $request->flutterwave_secret_key,
                'enabled' => $request->has('flutterwave_enabled') ? '1' : '0',
            ]
        ];

        $school->settings = $settings;
        $school->save();

        return redirect()->back()->with('success', 'Payment gateway configurations updated successfully.');
    }

    /**
     * Update grading scale items (min/max scores, points, grades, descriptions)
     */
    public function updateGradingScale(Request $request, \App\Models\GradingScale $scale)
    {
        $schoolId = $request->user()->school_id;

        if ($scale->school_id !== $schoolId) {
            abort(403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer',
            'items.*.grade' => 'required|string|max:10',
            'items.*.min_score' => 'required|numeric|min:0|max:100',
            'items.*.max_score' => 'required|numeric|min:0|max:100',
            'items.*.grade_point' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string|max:255',
        ]);

        // Sync items: identify deleted records
        $submittedIds = collect($request->items)->pluck('id')->filter()->toArray();
        \App\Models\GradingScaleItem::where('grading_scale_id', $scale->id)
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($request->items as $itemData) {
            if (!empty($itemData['id'])) {
                // Update existing item
                $item = \App\Models\GradingScaleItem::where('grading_scale_id', $scale->id)
                    ->where('id', $itemData['id'])
                    ->first();
                
                if ($item) {
                    $item->update([
                        'grade' => $itemData['grade'],
                        'min_score' => $itemData['min_score'],
                        'max_score' => $itemData['max_score'],
                        'grade_point' => $itemData['grade_point'],
                        'description' => $itemData['description'],
                    ]);
                }
            } else {
                // Create new item
                \App\Models\GradingScaleItem::create([
                    'grading_scale_id' => $scale->id,
                    'grade' => $itemData['grade'],
                    'min_score' => $itemData['min_score'],
                    'max_score' => $itemData['max_score'],
                    'grade_point' => $itemData['grade_point'],
                    'description' => $itemData['description'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Grading scale configuration updated successfully.');
    }
}
