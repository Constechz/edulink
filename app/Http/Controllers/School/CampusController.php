<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Services\Subscription\SubscriptionLimitService;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    protected $limitService;

    public function __construct(SubscriptionLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    /**
     * List all campuses.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $campuses = Campus::where('school_id', $schoolId)->get();

        return view('school.campuses', compact('campuses'));
    }

    /**
     * Store new campus.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Enforce campus subscription limit check
        $this->limitService->checkCampusLimit($schoolId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'principal_name' => 'nullable|string|max:255',
            'is_main' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        $data['school_id'] = $schoolId;

        // If setting this campus as main, reset other campuses
        if ($data['is_main']) {
            Campus::where('school_id', $schoolId)->update(['is_main' => false]);
        }

        Campus::create($data);

        return redirect()->back()->with('success', 'Campus registered successfully.');
    }

    /**
     * Update campus.
     */
    public function update(Request $request, Campus $campus)
    {
        $schoolId = $request->user()->school_id;

        if ($campus->school_id !== $schoolId) {
            abort(403, 'Unauthorized campus edit.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'principal_name' => 'nullable|string|max:255',
            'is_main' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        if ($data['is_main']) {
            Campus::where('school_id', $schoolId)->update(['is_main' => false]);
        }

        $campus->update($data);

        return redirect()->back()->with('success', 'Campus updated successfully.');
    }

    /**
     * Delete campus.
     */
    public function destroy(Request $request, Campus $campus)
    {
        $schoolId = $request->user()->school_id;

        if ($campus->school_id !== $schoolId) {
            abort(403, 'Unauthorized campus delete.');
        }

        $campus->delete();

        return redirect()->back()->with('success', 'Campus deleted successfully.');
    }
}
