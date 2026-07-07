<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\School;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
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
            $schoolId = null;
            $subdomainParam = $request->route() ? $request->route()->parameter('school_subdomain') : null;
            if ($subdomainParam) {
                $tenant = \Illuminate\Support\Facades\Cache::remember("school_subdomain_{$subdomainParam}", 3600, function () use ($subdomainParam) {
                    return School::where('subdomain', $subdomainParam)->first();
                });
                if ($tenant) {
                    $schoolId = $tenant->id;
                }
            } else {
                $schoolId = $request->header('X-School-ID') 
                            ?? $request->get('school_id') 
                            ?? $request->session()->get('school_id');
                
                // Fallback to authenticated user's school context
                if (!$schoolId && auth()->check()) {
                    $schoolId = auth()->user()->school_id;
                }
            }
            
            if ($schoolId && !$tenant) {
                $tenant = \Illuminate\Support\Facades\Cache::remember("school_{$schoolId}", 3600, function () use ($schoolId) {
                    return School::find($schoolId);
                });
            }
        } else {
            // Parse subdomain: schoolcode.edulink.edu.gh or schoolcode.edulink.local
            $domainParts = explode('.', $host);
            
            if (count($domainParts) > 2) {
                $subdomain = $domainParts[0];
                $mainHostParts = explode('.', parse_url(config('app.url'), PHP_URL_HOST));
                $mainSubdomain = $mainHostParts[0] ?? 'edulink';
                
                if ($subdomain !== 'www' && $subdomain !== 'admin' && $subdomain !== $mainSubdomain) {
                    $tenant = \Illuminate\Support\Facades\Cache::remember("school_subdomain_{$subdomain}", 3600, function () use ($subdomain) {
                        return School::where('subdomain', $subdomain)->first();
                    });
                }
            }
            
            // Check custom domain mapping
            if (!$tenant) {
                $tenant = \Illuminate\Support\Facades\Cache::remember("school_domain_{$host}", 3600, function () use ($host) {
                    return School::where('custom_domain', $host)
                                    ->orWhere('website_domain', $host)
                                    ->first();
                });
            }
        }

        if (!$tenant) {
            $schoolId = $request->header('X-School-ID') 
                        ?? $request->get('school_id') 
                        ?? $request->session()->get('school_id');
            
            if (!$schoolId && auth()->check()) {
                $schoolId = auth()->user()->school_id;
            }

            if ($schoolId) {
                $tenant = \Illuminate\Support\Facades\Cache::remember("school_{$schoolId}", 3600, function () use ($schoolId) {
                    return School::find($schoolId);
                });
            }
        }

        // Always bind 'tenant' to the container (even if null) to avoid BindingResolutionException
        app()->instance('tenant', $tenant);

        if ($tenant) {
            // Automatically mark trial as expired if it has passed
            if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
                $tenant->subscription_status = 'expired';
                $tenant->save();
            }

            if (!$tenant->is_active) {
                abort(403, 'This school account is suspended.');
            }

            $request->session()->put('school_id', $tenant->id);

            // Redirect expired tenants to the billing dashboard
            $route = $request->route();
            $routeName = $route ? $route->getName() : '';
            $exemptRoutes = [
                'logout',
                'school.billing.index',
                'school.billing.checkout',
            ];

            if ($tenant->subscription_status === 'expired' && !in_array($routeName, $exemptRoutes)) {
                if (auth()->check() && (!auth()->user()->role || auth()->user()->role->slug !== 'super-admin')) {
                    return redirect()->route('school.billing.index')
                        ->with('error', 'Your trial period or subscription has expired. Please upgrade your plan to restore full access.');
                }
            }

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
        }

        return $next($request);
    }
}
