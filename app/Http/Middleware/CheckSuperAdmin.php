<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role && $request->user()->role->slug === 'super-admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized. Super Admin access required.');
    }
}
