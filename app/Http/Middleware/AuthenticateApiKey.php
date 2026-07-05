<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Models\School;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Missing or invalid token format.'
            ], 401);
        }

        $plainToken = substr($header, 7);
        $tokenHash = hash('sha256', $plainToken);

        $apiKey = ApiKey::withoutGlobalScopes()
            ->where('token_hash', $tokenHash)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or expired API key.'
            ], 401);
        }

        $school = School::find($apiKey->school_id);
        if (!$school || !$school->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Associated school is inactive.'
            ], 401);
        }

        // Resolve Tenant
        app()->instance('tenant', $school);

        // Bind first active user of the school to request/auth context
        $user = User::withoutGlobalScopes()
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->first();

        if ($user) {
            auth()->setUser($user);
            $request->setUserResolver(fn () => $user);
        }

        return $next($request);
    }
}
