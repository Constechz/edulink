<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SafeguardingAuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    /**
     * Display the deployment documentation page.
     */
    public function deployment(Request $request)
    {
        return view('school.docs.deployment');
    }

    /**
     * Display the testing, security, and performance documentation page.
     */
    public function testing(Request $request)
    {
        return view('school.docs.testing');
    }

    /**
     * Run diagnostics and display the Security Audit & Compliance Center.
     */
    public function security(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Probing diagnostics
        $diagnostics = [];
        $passedCount = 0;

        // 1. Debug Mode Check
        $isDebugMode = config('app.debug');
        $diagnostics['debug_mode'] = [
            'title' => 'Application Debug Mode',
            'status' => !$isDebugMode ? 'passed' : 'failed',
            'value' => $isDebugMode ? 'Enabled (APP_DEBUG=true)' : 'Disabled (APP_DEBUG=false)',
            'description' => 'Debug mode must be disabled in production to prevent stack trace leaks and database exposure.',
            'recommendation' => 'Set APP_DEBUG=false inside your production .env file.'
        ];
        if (!$isDebugMode) $passedCount++;

        // 2. HTTPS Connection Check
        $isSecure = $request->secure() || str_starts_with(config('app.url'), 'https://');
        $diagnostics['https_status'] = [
            'title' => 'SSL/HTTPS Protocol',
            'status' => $isSecure ? 'passed' : 'warning',
            'value' => $isSecure ? 'Enforced' : 'Not Enforced',
            'description' => 'SSL certificates encrypt network traffic, preventing middle-man intercept attacks of login credentials.',
            'recommendation' => 'Configure Let\'s Encrypt SSL and verify APP_URL begins with https://.'
        ];
        if ($isSecure) $passedCount++;

        // 3. HttpOnly Session Cookies Check
        $isHttpOnly = config('session.http_only');
        $diagnostics['session_http_only'] = [
            'title' => 'HttpOnly Session Cookies',
            'status' => $isHttpOnly ? 'passed' : 'failed',
            'value' => $isHttpOnly ? 'Active (Secure)' : 'Inactive (Vulnerable)',
            'description' => 'Restricts client-side scripts from reading session identifier cookies, neutralizing XSS session hijack risks.',
            'recommendation' => 'Ensure SESSION_HTTP_ONLY=true is set inside config/session.php.'
        ];
        if ($isHttpOnly) $passedCount++;

        // 4. Secure Cookie Policy Check
        $isSecureCookie = config('session.secure');
        $diagnostics['session_secure'] = [
            'title' => 'Secure Session Cookies',
            'status' => $isSecureCookie ? 'passed' : 'warning',
            'value' => $isSecureCookie ? 'Active (Enforced)' : 'Inactive (Fallback)',
            'description' => 'Tells browsers to only transmit session identifiers over encrypted HTTPS protocol.',
            'recommendation' => 'Set SESSION_SECURE_COOKIE=true in your environment variables for production.'
        ];
        if ($isSecureCookie) $passedCount++;

        // 5. MFA Ratio Check
        $totalUsers = User::where('school_id', $schoolId)->count();
        $mfaUsersCount = User::where('school_id', $schoolId)->whereNotNull('mfa_secret')->count();
        $mfaEnrolled = $totalUsers > 0 && $mfaUsersCount > 0;
        
        $diagnostics['mfa_enrollment'] = [
            'title' => 'Multi-Factor Authentication (MFA)',
            'status' => $mfaEnrolled ? 'passed' : 'warning',
            'value' => $mfaEnrolled ? "{$mfaUsersCount} of {$totalUsers} accounts active" : '0 accounts active',
            'description' => 'Protects administrative accounts from credential attacks by requiring one-time OTP validations.',
            'recommendation' => 'Instruct administrative personnel to configure MFA profiles from security preferences.'
        ];
        if ($mfaEnrolled) $passedCount++;

        // Compute Compliance Rating
        $totalChecks = count($diagnostics);
        $complianceScore = round(($passedCount / $totalChecks) * 100);

        // Gather metrics
        $auditLogsCount = AuditLog::where('school_id', $schoolId)->count();
        $safeguardingLogsCount = SafeguardingAuditLog::count(); // System-wide audit table

        return view('school.docs.security', compact(
            'diagnostics', 
            'complianceScore', 
            'auditLogsCount', 
            'safeguardingLogsCount'
        ));
    }

    /**
     * Display the Help, Training & Roadmap reference portal.
     */
    public function help(Request $request)
    {
        $manuals = json_decode(\App\Models\SystemSetting::getVal('help_role_manuals', '[]'), true);
        $quickRefSba = json_decode(\App\Models\SystemSetting::getVal('help_quick_ref_sba', '[]'), true);
        $roadmap = json_decode(\App\Models\SystemSetting::getVal('help_roadmap', '[]'), true);
        $trainingVideos = json_decode(\App\Models\SystemSetting::getVal('help_training_videos', '[]'), true);

        return view('school.docs.help', compact('manuals', 'quickRefSba', 'roadmap', 'trainingVideos'));
    }
}
