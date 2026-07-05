<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifySessionHardening
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce for authenticated sessions
        if (Auth::check() || Auth::guard('platform_admin')->check()) {
            $session = $request->session();
            
            $currentIp = $request->ip();
            $currentUserAgent = $request->userAgent();

            // Set dynamic pinning values if they don't exist
            if (!$session->has('pinned_ip')) {
                $session->put('pinned_ip', $currentIp);
            }
            if (!$session->has('pinned_user_agent')) {
                $session->put('pinned_user_agent', $currentUserAgent ?: '');
            }

            // Invalidate session if IP or User Agent changes
            if ($session->get('pinned_ip') !== $currentIp || $session->get('pinned_user_agent') !== ($currentUserAgent ?: '')) {
                $isPlatformAdmin = Auth::guard('platform_admin')->check();

                Auth::logout();
                Auth::guard('platform_admin')->logout();
                
                $session->invalidate();
                $session->regenerateToken();

                $redirectTo = $isPlatformAdmin ? '/admin/login' : '/login';

                return redirect($redirectTo)->withErrors([
                    'session' => 'Your session has been terminated due to a change in IP or User Agent for security reasons.'
                ]);
            }
        }

        return $next($request);
    }
}
