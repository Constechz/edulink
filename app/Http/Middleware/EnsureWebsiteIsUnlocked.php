<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWebsiteIsUnlocked
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->school_id) {
            abort(403, 'Unauthorized school context.');
        }

        $school = School::find($user->school_id);
        
        if (!$school) {
            abort(404, 'Associated school not found.');
        }

        // Retrieve settings array
        $settings = $school->settings ?: [];
        $unlocked = isset($settings['website_builder_unlocked']) && $settings['website_builder_unlocked'] == true;

        if (!$unlocked) {
            return redirect()->route('school.billing.index')
                ->with('error', 'Please unlock the Custom Website Builder add-on in the billing panel to access this feature.');
        }

        return $next($request);
    }
}
