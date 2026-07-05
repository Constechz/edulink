<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\School;
use Symfony\Component\HttpFoundation\Response;

class IdentifySchoolByDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $tenant = null;

        // Dev environment or direct IP/localhost access
        $appNameLower = strtolower(config('app.name', 'EduLink'));
        if ($host === 'localhost' || $host === '127.0.0.1' || $host === 'edulink.local' || $host === "{$appNameLower}.local" || str_contains($host, 'ngrok')) {
            $schoolId = $request->get('school_id') ?? $request->session()->get('school_id');
            if ($schoolId) {
                $tenant = School::find($schoolId);
            }
        } else {
            // Parse subdomain: schoolcode.edulink.edu.gh or schoolcode.edulink.local
            $domainParts = explode('.', $host);
            
            if (count($domainParts) > 2) {
                $subdomain = $domainParts[0];
                if ($subdomain !== 'www' && $subdomain !== 'admin') {
                    $tenant = School::where('subdomain', $subdomain)->first();
                }
            }
            
            // Check custom domain mapping
            if (!$tenant) {
                $tenant = School::where('custom_domain', $host)
                                ->orWhere('website_domain', $host)
                                ->first();
            }
        }

        if (!$tenant) {
            abort(404, 'School website not found.');
        }

        if (!$tenant->is_active) {
            abort(403, 'This school website is currently unavailable.');
        }

        app()->instance('tenant', $tenant);
        $request->session()->put('school_id', $tenant->id);

        // Dynamic SMTP configurations for tenant school
        $emailConfig = $tenant->email_config;
        if ($emailConfig && !empty($emailConfig['host'])) {
            config([
                'mail.mailers.smtp.host' => $emailConfig['host'],
                'mail.mailers.smtp.port' => (int) ($emailConfig['port'] ?? 2525),
                'mail.mailers.smtp.encryption' => $emailConfig['encryption'] ?? 'tls',
                'mail.mailers.smtp.username' => $emailConfig['username'] ?? null,
                'mail.mailers.smtp.password' => $emailConfig['password'] ?? null,
                'mail.from.address' => $emailConfig['from_address'] ?? $tenant->email,
                'mail.from.name' => $emailConfig['from_name'] ?? $tenant->name,
            ]);

            // Reset resolved mailers to apply new config
            if (app()->bound('mail.manager')) {
                app('mail.manager')->forgetMailers();
            }
        }

        return $next($request);
    }
}
