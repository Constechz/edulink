<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class SuperAdminPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::withCount('schools')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allowedFeatures = $this->getAllowedFeatures();
        return view('super-admin.plans.create', compact('allowedFeatures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:plans,name',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_students' => 'required|integer',
            'max_staff' => 'required|integer',
            'max_campuses' => 'required|integer',
            'sms_credits_monthly' => 'required|integer|min:0',
            'storage_gb' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'features' => 'nullable|array',
        ]);

        Plan::create([
            'name' => $request->name,
            'price_monthly' => $request->price_monthly,
            'price_yearly' => $request->price_yearly,
            'max_students' => $request->max_students,
            'max_staff' => $request->max_staff,
            'max_campuses' => $request->max_campuses,
            'sms_credits_monthly' => $request->sms_credits_monthly,
            'storage_gb' => $request->storage_gb,
            'is_active' => $request->is_active,
            'features' => $request->features ?? [],
        ]);

        return redirect()->route('super-admin.plans.index')->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $plan = Plan::findOrFail($id);
        $allowedFeatures = $this->getAllowedFeatures();
        return view('super-admin.plans.edit', compact('plan', 'allowedFeatures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100|unique:plans,name,' . $plan->id,
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_students' => 'required|integer',
            'max_staff' => 'required|integer',
            'max_campuses' => 'required|integer',
            'sms_credits_monthly' => 'required|integer|min:0',
            'storage_gb' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'features' => 'nullable|array',
        ]);

        $plan->update([
            'name' => $request->name,
            'price_monthly' => $request->price_monthly,
            'price_yearly' => $request->price_yearly,
            'max_students' => $request->max_students,
            'max_staff' => $request->max_staff,
            'max_campuses' => $request->max_campuses,
            'sms_credits_monthly' => $request->sms_credits_monthly,
            'storage_gb' => $request->storage_gb,
            'is_active' => $request->is_active,
            'features' => $request->features ?? [],
        ]);

        return redirect()->route('super-admin.plans.index')->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);
        
        if ($plan->schools()->count() > 0) {
            $plan->update(['is_active' => false]);
            return redirect()->route('super-admin.plans.index')->with('success', 'Plan is assigned to active schools and cannot be deleted. It has been deactivated instead.');
        }

        $plan->delete();
        return redirect()->route('super-admin.plans.index')->with('success', 'Subscription plan deleted successfully.');
    }

    /**
     * Get the allowed modules/features array.
     */
    private function getAllowedFeatures(): array
    {
        return [
            'dashboard' => 'Dashboard View',
            'academics' => 'Academics Structure',
            'students' => 'Students Database',
            'finance' => 'Finance & Invoices',
            'website_builder' => 'Public Website Builder',
            'lms' => 'LMS Classroom',
            'portals' => 'Student/Parent Portals',
            'attendance' => 'Attendance Kiosk',
            'sms' => 'SMS Blast Gateway',
            'custom_domain' => 'Custom Domain Mapping',
            'api' => 'Developer API Integration',
            'ai_analytics' => 'AI Performance Analytics',
            'safeguarding' => 'Safeguarding Module',
            'monitoring' => 'Uptime & Server Monitoring',
            'hostels' => 'Hostel & Dorms Management',
            'transport' => 'Transport & Vehicle Tracking',
            'library' => 'Library Book Registry',
            'inventory' => 'Inventory & Asset Tracking',
            'hr_payroll' => 'HR & Payroll Ledger',
            'health_discipline' => 'Health & Discipline Record',
            'communication' => 'Communication Blast Gateway'
        ];
    }
}
