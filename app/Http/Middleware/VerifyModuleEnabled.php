<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyModuleEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();
        
        // Find school associated with user
        $school = $user ? \App\Models\School::find($user->school_id) : null;

        if (!$school) {
            abort(403, "Unauthorized school context.");
        }

        // Check if this module is globally paywalled for non-paid schools
        $paidOnlyModules = json_decode(\App\Models\SystemSetting::getVal('paid_only_modules', '[]'), true) ?: [];
        if (in_array($module, $paidOnlyModules)) {
            if ($school->subscription_status !== 'active') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => "The module '{$module}' requires a paid subscription.",
                        'redirect_billing' => true
                    ], 402);
                }
                return redirect()->route('school.billing.index')
                    ->with('error', "The module '" . ucfirst(str_replace('_', ' ', $module)) . "' is restricted to paid subscribers. Please complete a payment to unlock it.");
            }
        }

        if (!$school->isModuleEnabled($module)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => "The module '{$module}' is disabled or requires plan upgrade."], 403);
            }
            abort(403, "The requested module '{$module}' is disabled or requires plan upgrade.");
        }

        return $next($request);
    }
}
