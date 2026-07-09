<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form for guests.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for a new school tenant and admin user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'subdomain' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9\-]+$/i',
                'unique:schools,subdomain',
            ],
            'region' => 'required|string|max:100',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_phone' => 'required|string|min:9|max:20',
            'admin_password' => 'required|string|min:8|confirmed',
        ], [
            'subdomain.regex' => 'The subdomain may only contain letters, numbers, and dashes.',
            'subdomain.unique' => 'This subdomain is already taken.',
            'admin_email.unique' => 'This email address is already registered on our platform.',
        ]);

        try {
            $user = DB::transaction(function () use ($request) {
                // 1. Fetch default Plan (Free or cheapest plan)
                $plan = Plan::where('price_monthly', 0.00)->where('is_active', true)->first();
                if (!$plan) {
                    $plan = Plan::where('is_active', true)->orderBy('price_monthly', 'asc')->first();
                }
                
                if (!$plan) {
                    throw new \Exception('No active pricing plans configured on the platform.');
                }

                // Generate code from school name
                $cleanName = preg_replace('/[^a-zA-Z]/', '', $request->school_name);
                $code = strtoupper(substr($cleanName, 0, 4)) . mt_rand(10, 99);
                if (strlen($code) < 3) {
                    $code = 'SCH' . mt_rand(100, 999);
                }

                // 2. Create the School record
                $school = School::create([
                    'name' => $request->school_name,
                    'school_code' => $code,
                    'region' => $request->region,
                    'subdomain' => strtolower($request->subdomain),
                    'plan_id' => $plan->id,
                    'subscription_status' => 'trial',
                    'owner_name' => $request->admin_name,
                    'owner_email' => $request->admin_email,
                    'is_active' => false, // pending approval
                    'onboarding_completed' => false,
                    'branding' => [
                        'primary_color' => '#003366',
                        'accent_color' => '#FFD700',
                        'font_family' => 'Outfit',
                    ],
                ]);

                // 3. Get the school-admin role
                $role = Role::where('slug', 'school-admin')->first();
                if (!$role) {
                    $role = Role::create([
                        'name' => 'School Admin',
                        'slug' => 'school-admin',
                        'is_system' => true,
                    ]);
                }

                // 4. Create the School Administrator account
                $adminUser = User::create([
                    'school_id' => $school->id,
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'phone' => $request->admin_phone,
                    'password' => Hash::make($request->admin_password),
                    'role_id' => $role->id,
                    'is_active' => false, // pending approval
                ]);

                // Send email to Super Admin
                try {
                    $superAdminEmail = \App\Models\SystemSetting::getVal('super_admin_notification_email', 'admin@' . strtolower(config('app.name', 'edulink')) . '.com');
                    \Illuminate\Support\Facades\Mail::to($superAdminEmail)->send(new \App\Mail\SchoolRegisteredSuperAdminMail($school));
                } catch (\Exception $e) {
                    // Log or handle mail sending failures gracefully
                }

                // Send email to School Owner
                try {
                    \Illuminate\Support\Facades\Mail::to($school->owner_email)->send(new \App\Mail\SchoolRegistrationPendingMail($school));
                } catch (\Exception $e) {
                    // Log or handle mail sending failures gracefully
                }

                // Send SMS confirmation to School Owner
                try {
                    $smsTemplate = \App\Models\SystemSetting::getVal(
                        'school_registration_sms_template',
                        'Hello {admin_name}, your school - ({school_name}) registration is received, we will be calling you for further confirmation, after that your account information will be approved. Kindly join our WhatsApp channel ({whatsapp_link}) for more updates. Thanks for choosing us.'
                    );
                    $whatsappLink = \App\Models\SystemSetting::getVal('whatsapp_channel_url', 'https://whatsapp.com/channel/0029VaH4...');

                    $smsText = str_replace(
                        ['{admin_name}', '{school_name}', '{whatsapp_link}'],
                        [$request->admin_name, $request->school_name, $whatsappLink],
                        $smsTemplate
                    );

                    // Send SMS via unified service
                    $smsService = new \App\Services\SmsService();
                    $smsResult = $smsService->send($request->admin_phone, $smsText);

                    // Create SMS delivery log record
                    \App\Models\SmsDeliveryLog::create([
                        'school_id' => $school->id,
                        'phone_number' => $request->admin_phone,
                        'message_body' => $smsText,
                        'credits_used' => 1,
                        'status' => $smsResult['success'] ? 'delivered' : 'failed',
                        'reference' => $smsResult['reference'] ?? 'REG-' . mt_rand(100000, 999999)
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send school registration SMS: " . $e->getMessage());
                }

                return $adminUser;
            });

            // Dispatch auto-approval job with 5-minute delay
            if ($user && $user->school_id) {
                $school = \App\Models\School::find($user->school_id);
                if ($school) {
                    \App\Jobs\AutoApproveSchoolAndUser::dispatch($school)->delay(now()->addMinutes(5));
                }
            }

            // Redirect back to login with success message explaining approval pending status
            return redirect()->route('login')->with('success', 'Registration successful! Your school account is pending approval from the Super Admin. You will receive an email once it is approved.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
