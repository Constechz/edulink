<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    /**
     * Authenticate user (email + password) and return a new API key.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::withoutGlobalScopes()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid email or password.'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: User account is suspended.'
            ], 401);
        }

        // Generate a new plain API key for the user's school
        $plainToken = strtolower(config('app.name', 'EduLink')) . '_' . Str::random(40);
        $tokenHash = hash('sha256', $plainToken);

        $apiKey = ApiKey::create([
            'school_id' => $user->school_id,
            'name' => 'API Login Key - ' . $user->name . ' (' . now()->toDateString() . ')',
            'token_hash' => $tokenHash,
            'is_active' => true,
            'expires_at' => now()->addMonths(6), // expires in 6 months
        ]);

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'expires_at' => $apiKey->expires_at ? $apiKey->expires_at->toIso8601String() : null,
        ]);
    }
}
