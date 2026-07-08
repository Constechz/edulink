<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SmsCreditLedger;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\EmailLog;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class SuperAdminAnalyticsController extends Controller
{
    /**
     * Display Super Admin revenue, tenant subscription stats, and SMS pricing dashboard.
     */
    public function index(Request $request)
    {
        $schools = School::with('plan')->get();
        $plans = Plan::all();

        // Calculate SaaS revenue metrics
        $totalSchools = $schools->count();
        $activeSubsCount = $schools->where('subscription_status', 'active')->count();

        // Calculate MRR (Monthly Recurring Revenue)
        $mrr = 0.0;
        foreach ($schools as $school) {
            if ($school->subscription_status === 'active' && $school->plan) {
                // If school is active, add its plan's monthly price to MRR
                $mrr += (float) $school->plan->price_monthly;
            }
        }
        $arr = $mrr * 12;

        // Calculate SMS credits purchased vs remaining
        $totalSmsPurchased = SmsCreditLedger::where('type', 'purchase')->sum('credits');
        $totalSmsUsed = SmsCreditLedger::where('type', 'deduction')->sum('credits');
        
        // Latest purchases
        $transactions = SmsCreditLedger::with('school')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Global website unlock price
        $websiteUnlockPrice = SystemSetting::getVal('website_builder_unlock_price', '500.00');
        
        return view('super-admin.analytics', compact(
            'schools',
            'plans',
            'totalSchools',
            'activeSubsCount',
            'mrr',
            'arr',
            'totalSmsPurchased',
            'totalSmsUsed',
            'transactions',
            'websiteUnlockPrice'
        ));
    }

    /**
     * Manually update/override a school's plan or subscription status.
     */
    public function updateTenantSubscription(Request $request, $schoolId)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'subscription_status' => 'required|in:active,trial,expired,suspended',
            'trial_ends_at' => 'nullable|date',
            'api_access' => 'nullable|boolean',
        ]);

        $school = School::findOrFail($schoolId);
        $school->update([
            'plan_id' => $request->plan_id,
            'subscription_status' => $request->subscription_status,
            'trial_ends_at' => $request->trial_ends_at,
        ]);

        // Toggle API access feature flag
        $apiEnabled = $request->has('api_access') && $request->api_access == 1;
        \App\Models\FeatureFlag::updateOrCreate(
            ['school_id' => $school->id, 'feature_key' => 'api_access'],
            ['is_enabled' => $apiEnabled]
        );

        return redirect()->back()->with('success', "Tenant subscription and API settings overridden successfully for {$school->name}.");
    }

    /**
     * Manually credit or debit SMS units for a school.
     */
    public function updateSmsCredits(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'credits' => 'required|integer|min:1',
            'action_type' => 'required|in:purchase,deduction',
            'note' => 'nullable|string|max:255',
        ]);

        $schoolId = $request->school_id;
        $type = $request->action_type;
        $credits = $request->credits;

        // Fetch last balance
        $lastLedger = SmsCreditLedger::where('school_id', $schoolId)
            ->orderBy('id', 'desc')
            ->first();
        
        $currentBalance = $lastLedger ? $lastLedger->balance_after : 0;
        
        if ($type === 'purchase') {
            $newBalance = $currentBalance + $credits;
        } else {
            $newBalance = max(0, $currentBalance - $credits);
        }

        SmsCreditLedger::create([
            'school_id' => $schoolId,
            'type' => $type,
            'credits' => $credits,
            'balance_after' => $newBalance,
            'reference' => 'SA_ADMIN_' . time(),
            'note' => $request->note ?: 'Super Admin Manual adjustment.',
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', "Sms credits adjusted successfully.");
    }

    /**
     * Update system configuration settings.
     */
    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'website_builder_unlock_price' => 'required|numeric|min:0',
            'platform_paystack_public_key' => 'nullable|string',
            'platform_paystack_secret_key' => 'nullable|string',
            'platform_flutterwave_public_key' => 'nullable|string',
            'platform_flutterwave_secret_key' => 'nullable|string',
            'school_registration_sms_template' => 'required|string',
            'whatsapp_channel_url' => 'nullable|url',
            'platform_name' => 'required|string|max:100',
            'sms_gateway_provider' => 'required|string|in:simulation,arkesel,twilio,bms',
            'sms_gateway_api_key' => 'nullable|string',
            'sms_gateway_sender_id' => 'required|string|max:11',
            'report_card_price' => 'required|numeric|min:0',
            'portal_unlock_price' => 'required|numeric|min:0',
            'super_admin_notification_email' => 'required|email|max:255',
            'report_card_payment_enabled' => 'nullable|boolean',
            'favicon' => 'nullable|image|max:2048',
            'seo_meta_title' => 'nullable|string|max:200',
            'seo_meta_description' => 'nullable|string|max:500',
            'seo_meta_keywords' => 'nullable|string|max:500',
            'seo_google_analytics' => 'nullable|string|max:100',
            'seo_search_console' => 'nullable|string|max:200',
            'seo_social_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('favicon')) {
            try {
                $file = $request->file('favicon');
                $file->move(public_path(), 'favicon.png');
                copy(public_path('favicon.png'), public_path('favicon.ico'));
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['favicon' => 'Failed to upload favicon: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('seo_social_image')) {
            try {
                $file = $request->file('seo_social_image');
                $file->move(public_path(), 'seo_social.png');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['seo_social_image' => 'Failed to upload SEO social share image: ' . $e->getMessage()]);
            }
        }

        SystemSetting::setVal('website_builder_unlock_price', $request->website_builder_unlock_price);
        SystemSetting::setVal('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        SystemSetting::setVal('self_registration_enabled', $request->has('self_registration_enabled') ? '1' : '0');

        SystemSetting::setVal('platform_paystack_public_key', $request->platform_paystack_public_key ?? '');
        SystemSetting::setVal('platform_paystack_secret_key', $request->platform_paystack_secret_key ?? '');
        SystemSetting::setVal('platform_paystack_enabled', $request->has('platform_paystack_enabled') ? '1' : '0');

        SystemSetting::setVal('platform_flutterwave_public_key', $request->platform_flutterwave_public_key ?? '');
        SystemSetting::setVal('platform_flutterwave_secret_key', $request->platform_flutterwave_secret_key ?? '');
        SystemSetting::setVal('platform_flutterwave_enabled', $request->has('platform_flutterwave_enabled') ? '1' : '0');

        // Save paid only modules
        $paidOnly = $request->input('paid_only_modules', []);
        SystemSetting::setVal('paid_only_modules', json_encode($paidOnly));

        SystemSetting::setVal('school_registration_sms_template', $request->school_registration_sms_template);
        SystemSetting::setVal('whatsapp_channel_url', $request->whatsapp_channel_url ?? '');
        SystemSetting::setVal('platform_name', $request->platform_name);

        SystemSetting::setVal('sms_gateway_provider', $request->sms_gateway_provider);
        SystemSetting::setVal('sms_gateway_api_key', $request->sms_gateway_api_key ?? '');
        SystemSetting::setVal('sms_gateway_sender_id', $request->sms_gateway_sender_id);

        SystemSetting::setVal('report_card_price', $request->report_card_price);
        SystemSetting::setVal('portal_unlock_price', $request->portal_unlock_price);

        SystemSetting::setVal('super_admin_notification_email', $request->super_admin_notification_email);
        SystemSetting::setVal('report_card_payment_enabled', $request->has('report_card_payment_enabled') ? '1' : '0');

        SystemSetting::setVal('seo_meta_title', $request->seo_meta_title ?? '');
        SystemSetting::setVal('seo_meta_description', $request->seo_meta_description ?? '');
        SystemSetting::setVal('seo_meta_keywords', $request->seo_meta_keywords ?? '');
        SystemSetting::setVal('seo_google_analytics', $request->seo_google_analytics ?? '');
        SystemSetting::setVal('seo_search_console', $request->seo_search_console ?? '');

        return redirect()->back()->with('success', "Platform configuration updated successfully.");
    }

    /**
     * Dedicated SMS credits dashboard view.
     */
    public function smsCreditsIndex(Request $request)
    {
        $schools = School::all();
        $totalSmsPurchased = SmsCreditLedger::where('type', 'purchase')->sum('credits');
        $totalSmsUsed = SmsCreditLedger::where('type', 'deduction')->sum('credits');
        
        $transactions = SmsCreditLedger::with('school')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('super-admin.sms-credits', compact('schools', 'totalSmsPurchased', 'totalSmsUsed', 'transactions'));
    }

    /**
     * Dedicated Access Logs dashboard view.
     */
    public function accessLogsIndex(Request $request)
    {
        $logs = AuditLog::with(['school', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.access-logs', compact('logs'));
    }

    /**
     * Dedicated Settings console view.
     */
    public function settingsIndex(Request $request)
    {
        $websiteUnlockPrice = SystemSetting::getVal('website_builder_unlock_price', '500.00');
        $maintenanceMode = SystemSetting::getVal('maintenance_mode', '0');
        $selfRegistration = SystemSetting::getVal('self_registration_enabled', '1');

        $paystackPublicKey = SystemSetting::getVal('platform_paystack_public_key', '');
        $paystackSecretKey = SystemSetting::getVal('platform_paystack_secret_key', '');
        $paystackEnabled = SystemSetting::getVal('platform_paystack_enabled', '0');

        $flutterwavePublicKey = SystemSetting::getVal('platform_flutterwave_public_key', '');
        $flutterwaveSecretKey = SystemSetting::getVal('platform_flutterwave_secret_key', '');
        $flutterwaveEnabled = SystemSetting::getVal('platform_flutterwave_enabled', '0');

        $paidOnlyModules = json_decode(SystemSetting::getVal('paid_only_modules', '[]'), true) ?: [];

        $schoolRegistrationSmsTemplate = SystemSetting::getVal(
            'school_registration_sms_template',
            'Hello {admin_name}, your school - ({school_name}) registration is received, we will be calling you for further confirmation, after that your account information will be approved. Kindly join our WhatsApp channel ({whatsapp_link}) for more updates. Thanks for choosing us.'
        );
        $whatsappChannelUrl = SystemSetting::getVal('whatsapp_channel_url', 'https://whatsapp.com/channel/0029VaH4...');
        $platformName = SystemSetting::getVal('platform_name', 'EduLink');

        $smsGatewayProvider = SystemSetting::getVal('sms_gateway_provider', 'simulation');
        $smsGatewayApiKey = SystemSetting::getVal('sms_gateway_api_key', '');
        $smsGatewaySenderId = SystemSetting::getVal('sms_gateway_sender_id', 'EduLink');

        $reportCardPrice = SystemSetting::getVal('report_card_price', '0.20');
        $portalUnlockPrice = SystemSetting::getVal('portal_unlock_price', '200.00');

        $superAdminNotificationEmail = SystemSetting::getVal('super_admin_notification_email', 'admin@' . strtolower(config('app.name', 'edulink')) . '.com');
        $reportCardPaymentEnabled = SystemSetting::getVal('report_card_payment_enabled', '1');

        $seoMetaTitle = SystemSetting::getVal('seo_meta_title', 'EduLink | Premium School Management System & ERP');
        $seoMetaDescription = SystemSetting::getVal('seo_meta_description', 'All-in-one school ERP system to manage grading, invoicing, attendance, parent portals, and SMS alerts.');
        $seoMetaKeywords = SystemSetting::getVal('seo_meta_keywords', 'school management system, school ERP, school software Ghana, report card builder');
        $seoGoogleAnalytics = SystemSetting::getVal('seo_google_analytics', '');
        $seoSearchConsole = SystemSetting::getVal('seo_search_console', '');

        return view('super-admin.settings', compact(
            'websiteUnlockPrice', 'maintenanceMode', 'selfRegistration',
            'paystackPublicKey', 'paystackSecretKey', 'paystackEnabled',
            'flutterwavePublicKey', 'flutterwaveSecretKey', 'flutterwaveEnabled',
            'paidOnlyModules', 'schoolRegistrationSmsTemplate', 'whatsappChannelUrl',
            'platformName', 'smsGatewayProvider', 'smsGatewayApiKey', 'smsGatewaySenderId',
            'reportCardPrice', 'portalUnlockPrice', 'superAdminNotificationEmail', 'reportCardPaymentEnabled',
            'seoMetaTitle', 'seoMetaDescription', 'seoMetaKeywords', 'seoGoogleAnalytics', 'seoSearchConsole'
        ));
    }

    /**
     * Send a test SMS using the configured settings.
     */
    public function sendTestSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
            'test_message' => 'required|string|max:160',
        ]);

        $smsService = new \App\Services\SmsService();
        $result = $smsService->send($request->test_phone, $request->test_message);

        if ($result['success']) {
            $ref = $result['reference'] ?? 'N/A';
            return redirect()->back()->with('success', "Test SMS sent successfully! Reference: {$ref}");
        }

        return redirect()->back()->withErrors(['test_sms' => 'Test SMS dispatch failed: ' . ($result['error'] ?? 'Unknown gateway error.')]);
    }

    /**
     * Approve and activate a registered school.
     */
    public function approveSchool(Request $request, $schoolId)
    {
        $school = School::findOrFail($schoolId);
        $school->update([
            'is_active' => true,
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);

        // Activate user(s) belonging to the school
        User::withoutGlobalScopes()->where('school_id', $school->id)->update(['is_active' => true]);

        // Send approval confirmation email to the school owner
        try {
            \Illuminate\Support\Facades\Mail::to($school->owner_email)->send(new \App\Mail\SchoolApprovedMail($school));
        } catch (\Exception $e) {
            // Log error or let it fail gracefully
        }

        return redirect()->back()->with('success', "School '{$school->name}' was successfully approved and activated.");
    }

    /**
     * Dedicated Email settings and logs view.
     */
    public function emailSettingsIndex(Request $request)
    {
        $smtpHost = SystemSetting::getVal('smtp_host', '127.0.0.1');
        $smtpPort = SystemSetting::getVal('smtp_port', '2525');
        $smtpEncryption = SystemSetting::getVal('smtp_encryption', 'tls');
        $smtpUsername = SystemSetting::getVal('smtp_username', '');
        $smtpPassword = SystemSetting::getVal('smtp_password', '');
        $mailFromAddress = SystemSetting::getVal('mail_from_address', 'hello@example.com');
        $mailFromName = SystemSetting::getVal('mail_from_name', config('app.name', 'EduLink') . ' Ghana ERP');

        // Outgoing email logs query
        $emailLogs = EmailLog::orderBy('created_at', 'desc')->paginate(15);
        $schools = School::orderBy('name', 'asc')->get();

        // Calculate statistics for UI summary cards
        $totalEmails = EmailLog::count();
        $sentEmails = EmailLog::where('status', 'sent')->count();
        $failedEmails = EmailLog::where('status', 'failed')->count();
        
        $successRate = $totalEmails > 0 ? round(($sentEmails / $totalEmails) * 100, 1) : 100.0;
        $failedRate = $totalEmails > 0 ? round(($failedEmails / $totalEmails) * 100, 1) : 0.0;

        return view('super-admin.email-settings', compact(
            'smtpHost', 'smtpPort', 'smtpEncryption', 'smtpUsername', 'smtpPassword', 
            'mailFromAddress', 'mailFromName', 'emailLogs', 'schools',
            'totalEmails', 'sentEmails', 'failedEmails', 'successRate', 'failedRate'
        ));
    }

    /**
     * Update dynamic email SMTP parameters.
     */
    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_encryption' => 'required|string|in:tls,ssl,none',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        SystemSetting::setVal('smtp_host', $request->smtp_host);
        SystemSetting::setVal('smtp_port', $request->smtp_port);
        SystemSetting::setVal('smtp_encryption', $request->smtp_encryption);
        SystemSetting::setVal('smtp_username', $request->smtp_username ?: '');
        SystemSetting::setVal('smtp_password', $request->smtp_password ?: '');
        SystemSetting::setVal('mail_from_address', $request->mail_from_address);
        SystemSetting::setVal('mail_from_name', $request->mail_from_name);

        return redirect()->back()->with('success', 'Email Gateway configuration updated successfully.');
    }

    /**
     * Send custom emails based on selected target audience.
     */
    public function sendCustomEmail(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:all_admins,all_users,specific_school,specific_user',
            'school_id' => 'required_if:target_type,specific_school|nullable|exists:schools,id',
            'specific_email' => 'required_if:target_type,specific_user|nullable|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $recipients = [];

        if ($request->target_type === 'all_admins') {
            // Get all school admin role users globally
            $recipients = User::withoutGlobalScopes()
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'school-admin');
                })
                ->where('is_active', true)
                ->pluck('email')
                ->toArray();
        } elseif ($request->target_type === 'all_users') {
            // Get all active users globally
            $recipients = User::withoutGlobalScopes()
                ->where('is_active', true)
                ->pluck('email')
                ->toArray();
        } elseif ($request->target_type === 'specific_school') {
            // Get all active users of a specific school
            $recipients = User::withoutGlobalScopes()
                ->where('school_id', $request->school_id)
                ->where('is_active', true)
                ->pluck('email')
                ->toArray();
        } elseif ($request->target_type === 'specific_user') {
            $recipients = [$request->specific_email];
        }

        if (empty($recipients)) {
            return redirect()->back()->withErrors(['error' => 'No active recipients found for the selected target.']);
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)
                    ->send(new \App\Mail\SuperAdminCustomMail($request->subject, $request->body));
                $sentCount++;

                // Trigger portal notification
                $user = User::withoutGlobalScopes()->where('email', $email)->first();
                if ($user) {
                    NotificationLog::create([
                        'school_id' => $user->school_id,
                        'user_id' => $user->id,
                        'title' => $request->subject,
                        'body' => strip_tags($request->body),
                        'type' => 'broadcast',
                        'is_read' => false,
                    ]);
                }
            } catch (\Exception $e) {
                $failedCount++;
                EmailLog::create([
                    'recipient_email' => $email,
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        if ($failedCount > 0) {
            return redirect()->back()->with('success', "Emails sent successfully to {$sentCount} recipient(s). Fails: {$failedCount}.");
        }

        return redirect()->back()->with('success', "Emails sent successfully to {$sentCount} recipient(s).");
    }

    /**
     * Test SMTP configuration on-the-fly.
     */
    public function testEmailSettings(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $recipient = $request->test_email;

        $smtpHost = SystemSetting::getVal('smtp_host', '127.0.0.1');
        $smtpPort = SystemSetting::getVal('smtp_port', '2525');
        $smtpEncryption = SystemSetting::getVal('smtp_encryption', 'tls');
        $smtpUsername = SystemSetting::getVal('smtp_username', '');
        $smtpPassword = SystemSetting::getVal('smtp_password', '');
        $mailFromAddress = SystemSetting::getVal('mail_from_address', 'hello@example.com');
        $mailFromName = SystemSetting::getVal('mail_from_name', config('app.name', 'EduLink') . ' Ghana ERP');

        // Temporarily override mailer configurations
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPort,
            'mail.mailers.smtp.encryption' => $smtpEncryption === 'none' ? null : $smtpEncryption,
            'mail.mailers.smtp.username' => $smtpUsername,
            'mail.mailers.smtp.password' => $smtpPassword,
            'mail.from.address' => $mailFromAddress,
            'mail.from.name' => $mailFromName,
        ]);

        // Purge mailer cache to force reload configuration
        \Illuminate\Support\Facades\Mail::purge('smtp');

        try {
            \Illuminate\Support\Facades\Mail::to($recipient)->send(
                new \App\Mail\SuperAdminCustomMail(
                    'SMTP Gateway Test Connection',
                    'This is a diagnostic test email to verify that your SMTP server gateway settings are active and working correctly. No further action is required.'
                )
            );

            // Log test mail in logs table
            EmailLog::create([
                'recipient_email' => $recipient,
                'subject' => 'SMTP Gateway Test Connection',
                'body' => 'This is a diagnostic test email to verify that your SMTP server gateway settings are active and working correctly.',
                'status' => 'sent',
            ]);

            return redirect()->back()->with('success', "Test email successfully sent to {$recipient}! Your SMTP server is configured correctly.");
        } catch (\Exception $e) {
            // Log failed test mail in logs table
            EmailLog::create([
                'recipient_email' => $recipient,
                'subject' => 'SMTP Gateway Test Connection',
                'body' => 'This is a diagnostic test email to verify that your SMTP server gateway settings are active and working correctly.',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'smtp_error' => 'SMTP connection test failed. Server returned: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete an outgoing email log entry.
     */
    public function destroyEmailLog($id)
    {
        $log = EmailLog::findOrFail($id);
        $log->delete();

        return redirect()->back()->with('success', 'Email transmission log deleted successfully.');
    }

    /**
     * Impersonate a tenant school admin user account.
     */
    public function impersonateSchool($schoolId)
    {
        $school = School::findOrFail($schoolId);
        
        $targetUser = User::where('school_id', $school->id)
            ->whereHas('role', function ($q) {
                $q->where('slug', 'school-admin');
            })->first();

        if (!$targetUser) {
            return redirect()->back()->withErrors([
                'impersonation_error' => "No active administrator account was found for school: {$school->name}."
            ]);
        }

        // Keep track of the original super admin ID
        session()->put('impersonator_id', \Illuminate\Support\Facades\Auth::id());

        // Create an audit trail log entry
        AuditLog::create([
            'school_id' => $school->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'impersonate_start',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => json_encode(['impersonator_id' => \Illuminate\Support\Facades\Auth::id()]),
            'new_values' => json_encode(['target_user_id' => $targetUser->id])
        ]);

        \Illuminate\Support\Facades\Auth::login($targetUser);

        return redirect()->route('dashboard')->with('success', "Impersonating administrator user {$targetUser->name} for school: {$school->name}.");
    }

    /**
     * End impersonation session and return to Super Admin dashboard.
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $impersonatorId = session()->pull('impersonator_id');
        $impersonator = User::findOrFail($impersonatorId);

        // Access logs audit trail
        AuditLog::create([
            'school_id' => \Illuminate\Support\Facades\Auth::user()->school_id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'impersonate_stop',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => json_encode(['target_user_id' => \Illuminate\Support\Facades\Auth::id()]),
            'new_values' => json_encode(['impersonator_id' => $impersonatorId])
        ]);

        \Illuminate\Support\Facades\Auth::login($impersonator);

        return redirect()->route('super-admin.dashboard')->with('success', "Impersonation session ended. Returned to Super Admin Panel.");
    }

    /**
     * Display environment settings dashboard.
     */
    public function envSettingsIndex(Request $request)
    {
        $envPath = base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';
        $backupExists = file_exists(base_path('.env.bak'));
        $backupTime = $backupExists ? date('Y-m-d H:i:s', filemtime(base_path('.env.bak'))) : null;

        return view('super-admin.env-settings', compact('envContent', 'backupExists', 'backupTime'));
    }

    /**
     * Save updated environment settings.
     */
    public function updateEnvSettings(Request $request)
    {
        $request->validate([
            'env_content' => 'required|string',
        ]);

        $envPath = base_path('.env');

        // Backup existing env file
        if (file_exists($envPath)) {
            copy($envPath, base_path('.env.bak'));
        }

        // Save new content
        file_put_contents($envPath, $request->env_content);

        // Clear Laravel config cache
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to clear configuration cache after updating .env: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Environment settings (.env) updated successfully! Config cache has been cleared and a backup created at .env.bak.');
    }

    /**
     * Restore environment settings from backup.
     */
    public function restoreEnvSettings()
    {
        $backupPath = base_path('.env.bak');
        $envPath = base_path('.env');

        if (!file_exists($backupPath)) {
            return redirect()->back()->withErrors(['env' => 'No backup file (.env.bak) found.']);
        }

        // Restore backup
        copy($backupPath, $envPath);

        // Clear Laravel config cache
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to clear configuration cache after restoring .env: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Environment settings (.env) restored successfully from .env.bak! Config cache cleared.');
    }

    /**
     * Dispatch a system-wide or school-specific in-app notification blast.
     */
    public function sendSystemNotification(Request $request)
    {
        $request->validate([
            'notif_target' => 'required|string|in:all_admins,all_users,specific_school,all_staff,all_parents,all_students',
            'notif_school_id' => 'nullable|integer|exists:schools,id',
            'notif_title' => 'required|string|max:150',
            'notif_body' => 'required|string|max:1000',
        ]);

        $title = $request->notif_title;
        $body = $request->notif_body;
        $target = $request->notif_target;

        // Query active users depending on target
        $query = \App\Models\User::withoutGlobalScopes()->where('is_active', true);

        if ($target === 'specific_school') {
            if (!$request->notif_school_id) {
                return redirect()->back()->withErrors(['notif_school_id' => 'Please select a specific school for the targeted blast.']);
            }
            $query->where('school_id', $request->notif_school_id);
        } elseif ($target === 'all_admins') {
            // Find school admins by checking role slug 'school-admin'
            $query->whereHas('role', function($q) {
                $q->where('slug', 'school-admin');
            });
        } elseif ($target === 'all_staff') {
            // Find school staff (teachers, HODs, headteachers)
            $query->whereHas('role', function($q) {
                $q->whereIn('slug', ['teacher', 'hod', 'headteacher', 'staff']);
            });
        } elseif ($target === 'all_parents') {
            // Find parents
            $query->whereHas('role', function($q) {
                $q->where('slug', 'parent');
            });
        } elseif ($target === 'all_students') {
            // Find students
            $query->whereHas('role', function($q) {
                $q->where('slug', 'student');
            });
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return redirect()->back()->withErrors(['notif_target' => 'No active users match the selected target audience.']);
        }

        // Loop and dispatch notification logs
        foreach ($users as $user) {
            \App\Models\NotificationLog::create([
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'type' => 'system',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'In-app notification blast dispatched successfully to ' . number_format($users->count()) . ' users.');
    }
}
