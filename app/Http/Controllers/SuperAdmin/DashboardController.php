<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Plan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Platform level dashboard queries - bypass tenant scope using withoutGlobalScopes()
        $totalSchools = School::withoutGlobalScopes()->count();
        $totalPlans = Plan::withoutGlobalScopes()->count();
        $totalUsers = User::withoutGlobalScopes()->count();
        
        // Let's get schools grouped by subscription status
        $statusCounts = School::withoutGlobalScopes()
            ->selectRaw('subscription_status, count(*) as count')
            ->groupBy('subscription_status')
            ->get()
            ->pluck('count', 'subscription_status')
            ->toArray();
            
        // Let's get schools grouped by region for geographical distribution in Ghana
        $regionCounts = School::withoutGlobalScopes()
            ->selectRaw('region, count(*) as count')
            ->whereNotNull('region')
            ->groupBy('region')
            ->get();

        // Query Registered Schools with optional search
        $schoolQuery = School::withoutGlobalScopes()->with('plan');
        if ($request->filled('school_search')) {
            $search = $request->school_search;
            $schoolQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('school_code', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%");
            });
        }
        $schoolsList = $schoolQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'schools_page')->withQueryString();

        // Query All Users across schools & roles with search & filter
        $userQuery = User::withoutGlobalScopes()->with(['school', 'role']);
        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $userQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('role_filter')) {
            $roleFilter = $request->role_filter;
            $userQuery->whereHas('role', function($q) use ($roleFilter) {
                $q->where('slug', $roleFilter);
            });
        }
        $usersList = $userQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'users_page')->withQueryString();

        // Load roles list for filters (bypassing SchoolScope)
        $rolesList = Role::withoutGlobalScopes()->select('name', 'slug')->distinct()->get();

        return view('super-admin.dashboard', compact(
            'totalSchools',
            'totalPlans',
            'totalUsers',
            'statusCounts',
            'regionCounts',
            'schoolsList',
            'usersList',
            'rolesList'
        ));
    }

    /**
     * Toggle status (is_active) of a platform user.
     */
    public function toggleUserStatus($userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User '{$user->name}' was successfully {$status}.");
    }

    /**
     * Toggle status (is_active) of a school.
     */
    public function toggleSchoolStatus($schoolId)
    {
        $school = School::withoutGlobalScopes()->findOrFail($schoolId);
        $school->is_active = !$school->is_active;
        $school->save();

        // Also update users of this school if deactivated
        if (!$school->is_active) {
            User::withoutGlobalScopes()->where('school_id', $school->id)->update(['is_active' => false]);
        }

        $status = $school->is_active ? 'activated' : 'suspended';
        return redirect()->back()->with('success', "School '{$school->name}' was successfully {$status}.");
    }
}
