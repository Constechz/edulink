<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Plan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Cache platform-wide aggregate counts for 60 seconds to make page load instantaneous
        $totalSchools = Cache::remember('sa_total_schools', 60, function () {
            return School::withoutGlobalScopes()->count();
        });

        $totalPlans = Cache::remember('sa_total_plans', 60, function () {
            return Plan::withoutGlobalScopes()->count();
        });

        $totalUsers = Cache::remember('sa_total_users', 60, function () {
            return User::withoutGlobalScopes()->count();
        });
        
        // Schools grouped by subscription status (cached 60 seconds)
        $statusCounts = Cache::remember('sa_status_counts', 60, function () {
            return School::withoutGlobalScopes()
                ->selectRaw('subscription_status, count(*) as count')
                ->groupBy('subscription_status')
                ->pluck('count', 'subscription_status')
                ->toArray();
        });
            
        // Schools grouped by region in Ghana (cached 60 seconds)
        $regionCounts = Cache::remember('sa_region_counts', 60, function () {
            return School::withoutGlobalScopes()
                ->selectRaw('region, count(*) as count')
                ->whereNotNull('region')
                ->where('region', '!=', '')
                ->groupBy('region')
                ->get();
        });

        // Query Registered Schools with optimized column selection and eager loading
        $schoolQuery = School::withoutGlobalScopes()
            ->select('id', 'name', 'school_code', 'owner_name', 'owner_email', 'phone', 'region', 'subscription_status', 'plan_id', 'is_active', 'created_at')
            ->with(['plan' => function($q) {
                $q->select('id', 'name', 'price_monthly');
            }]);

        if ($request->filled('school_search')) {
            $search = trim($request->school_search);
            $schoolQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('school_code', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%");
            });
        }
        $schoolsList = $schoolQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'schools_page')->withQueryString();

        // Query All Users with optimized column selection and eager loading
        $userQuery = User::withoutGlobalScopes()
            ->select('id', 'name', 'email', 'school_id', 'role_id', 'is_active', 'created_at')
            ->with([
                'school' => function($q) {
                    $q->select('id', 'name', 'school_code');
                },
                'role' => function($q) {
                    $q->select('id', 'name', 'slug');
                }
            ]);

        if ($request->filled('user_search')) {
            $search = trim($request->user_search);
            $userQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('role_filter')) {
            $roleFilter = trim($request->role_filter);
            $userQuery->whereHas('role', function($q) use ($roleFilter) {
                $q->where('slug', $roleFilter);
            });
        }
        $usersList = $userQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'users_page')->withQueryString();

        // Cached list of distinct roles for the user filter dropdown
        $rolesList = Cache::remember('sa_roles_list', 120, function () {
            return Role::withoutGlobalScopes()->select('name', 'slug')->distinct()->get();
        });

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
     * Clear cached metrics on data modifications.
     */
    private function clearDashboardCache(): void
    {
        Cache::forget('sa_total_schools');
        Cache::forget('sa_total_plans');
        Cache::forget('sa_total_users');
        Cache::forget('sa_status_counts');
        Cache::forget('sa_region_counts');
        Cache::forget('sa_roles_list');
    }

    /**
     * Toggle status (is_active) of a platform user.
     */
    public function toggleUserStatus($userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        $this->clearDashboardCache();

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

        $this->clearDashboardCache();

        $status = $school->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "School '{$school->name}' was successfully {$status}.");
    }
}
