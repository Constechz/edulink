<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of the API keys.
     */
    public function index(Request $request)
    {
        $school = $request->user()->school;
        if (!$school || !$school->isFeatureEnabled('api_access', false)) {
            abort(403, 'API Developer Access is not enabled for your institution. Please contact platform support.');
        }

        $schoolId = $school->id;
        $keys = ApiKey::where('school_id', $schoolId)->orderBy('created_at', 'desc')->get();

        return view('school.api_keys', compact('keys'));
    }

    /**
     * Store a newly created API key.
     */
    public function store(Request $request)
    {
        $school = $request->user()->school;
        if (!$school || !$school->isFeatureEnabled('api_access', false)) {
            abort(403, 'API Developer Access is not enabled for your institution.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $schoolId = $school->id;
        $plainToken = strtolower(config('app.name', 'EduLink')) . '_' . Str::random(40);
        $tokenHash = hash('sha256', $plainToken);

        ApiKey::create([
            'school_id' => $schoolId,
            'name' => $request->name,
            'token_hash' => $tokenHash,
            'is_active' => true,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->back()
            ->with('success', 'API key generated successfully.')
            ->with('plain_token', $plainToken);
    }

    /**
     * Toggle active state or delete API key.
     */
    public function destroy(Request $request, $id)
    {
        $school = $request->user()->school;
        if (!$school || !$school->isFeatureEnabled('api_access', false)) {
            abort(403, 'API Developer Access is not enabled for your institution.');
        }

        $schoolId = $school->id;
        $key = ApiKey::where('school_id', $schoolId)->findOrFail($id);
        $key->delete();

        return redirect()->back()->with('success', 'API key revoked successfully.');
    }
}
