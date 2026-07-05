<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Services\Subscription\SubscriptionLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class StaffController extends Controller
{
    protected $limitService;

    public function __construct(SubscriptionLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    /**
     * List all staff accounts.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        
        $staffMembers = Staff::where('school_id', $schoolId)->with(['user', 'campus'])->get();
        $campuses = Campus::where('school_id', $schoolId)->get();
        
        // Fetch global template roles and school custom roles
        $roles = Role::whereNull('school_id')->orWhere('school_id', $schoolId)->get();

        // Fetch classes and streams for classroom assignment
        $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->with('streams')->get();
        $allStreams = \App\Models\Stream::where('school_id', $schoolId)->with('class')->get();

        return view('school.staff', compact('staffMembers', 'campuses', 'roles', 'classes', 'allStreams'));
    }

    /**
     * Store a new staff account.
     */
    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Enforce staff limit check against subscription plan details
        $this->limitService->checkStaffLimit($schoolId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'campus_id' => 'required|exists:campuses,id',
            'designation' => 'required|string|max:255',
            'staff_number' => 'nullable|string|max:100',
            'assigned_stream_id' => 'nullable|exists:streams,id',
        ]);

        // Email must be unique per school tenant
        $exists = User::where('school_id', $schoolId)->where('email', $request->email)->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['email' => 'A user account with this email address already exists in this school.']);
        }

        try {
            DB::transaction(function () use ($request, $schoolId) {
                // 1. Create User
                $user = User::create([
                    'school_id' => $schoolId,
                    'campus_id' => $request->campus_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id' => $request->role_id,
                    'is_active' => true,
                ]);

                // 2. Create Staff link
                Staff::create([
                    'user_id' => $user->id,
                    'school_id' => $schoolId,
                    'campus_id' => $request->campus_id,
                    'staff_number' => $request->staff_number ?: 'STF-' . sprintf('%04d', mt_rand(1, 9999)),
                    'designation' => $request->designation,
                    'date_joined' => now(),
                ]);

                // 3. Assign stream teacher if role is class-teacher
                $userRole = Role::find($request->role_id);
                if ($userRole && $userRole->slug === 'class-teacher' && $request->assigned_stream_id) {
                    $stream = \App\Models\Stream::find($request->assigned_stream_id);
                    if ($stream && $stream->school_id === $schoolId) {
                        $stream->update(['class_teacher_id' => $user->id]);
                        $class = $stream->class;
                        if ($class && is_null($class->class_teacher_id)) {
                            $class->update(['class_teacher_id' => $user->id]);
                        }
                    }
                }
            });

            return redirect()->back()->with('success', 'Staff account registered successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to register staff account: ' . $e->getMessage()]);
        }
    }

    /**
     * Update staff details.
     */
    public function update(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff edit.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'campus_id' => 'required|exists:campuses,id',
            'designation' => 'required|string|max:255',
            'staff_number' => 'required|string|max:100',
            'is_active' => 'required|boolean',
            'assigned_stream_id' => 'nullable|exists:streams,id',
        ]);

        try {
            DB::transaction(function () use ($request, $staff, $schoolId) {
                // Update linked user
                $user = $staff->user;
                if ($user) {
                    $user->update([
                        'name' => $request->name,
                        'role_id' => $request->role_id,
                        'campus_id' => $request->campus_id,
                        'is_active' => $request->is_active,
                    ]);

                    // Update class teacher assignment
                    $userRole = Role::find($request->role_id);
                    if ($userRole && $userRole->slug === 'class-teacher') {
                        // Clear old assignments for this teacher
                        \App\Models\Stream::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                        \App\Models\SchoolClass::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                        
                        if ($request->assigned_stream_id) {
                            $stream = \App\Models\Stream::find($request->assigned_stream_id);
                            if ($stream && $stream->school_id === $schoolId) {
                                $stream->update(['class_teacher_id' => $user->id]);
                                $class = $stream->class;
                                if ($class && is_null($class->class_teacher_id)) {
                                    $class->update(['class_teacher_id' => $user->id]);
                                }
                            }
                        }
                    } else {
                        // If not class-teacher, clear allocations
                        \App\Models\Stream::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                        \App\Models\SchoolClass::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                    }
                }

                // Update staff record
                $staff->update([
                    'campus_id' => $request->campus_id,
                    'staff_number' => $request->staff_number,
                    'designation' => $request->designation,
                ]);
            });

            return redirect()->back()->with('success', 'Staff account updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update staff account: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle staff activation status.
     */
    public function toggleStatus(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff action.');
        }

        $user = $staff->user;
        if ($user) {
            $user->update([
                'is_active' => !$user->is_active,
            ]);
        }

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Staff account successfully {$status}.");
    }

    /**
     * Delete staff account (soft delete).
     */
    public function destroy(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff delete.');
        }

        try {
            DB::transaction(function () use ($staff) {
                $user = $staff->user;
                if ($user) {
                    $user->delete();
                }
                $staff->delete();
            });

            return redirect()->back()->with('success', 'Staff account removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to remove staff account: ' . $e->getMessage()]);
        }
    }

    /**
     * Report a staff member (log incident/issue).
     */
    public function report(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff action.');
        }

        $request->validate([
            'category' => 'required|string|max:255',
            'severity' => 'required|string|in:Low,Medium,High,Critical',
            'description' => 'required|string',
        ]);

        \App\Models\StaffReport::create([
            'school_id' => $schoolId,
            'staff_id' => $staff->id,
            'reported_by' => $request->user()->id,
            'category' => $request->category,
            'severity' => $request->severity,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // If severity is critical and option is selected, deactivate account
        if ($request->severity === 'Critical' && $request->has('deactivate_account') && $request->deactivate_account == '1') {
            $user = $staff->user;
            if ($user) {
                $user->update(['is_active' => false]);
            }
            $msg = 'Staff report logged and account deactivated successfully.';
        } else {
            $msg = 'Staff report logged successfully.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Generate bulk staff details PDF report.
     */
    public function printPdf(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $school = \App\Models\School::find($schoolId);
        
        $staffMembers = Staff::where('school_id', $schoolId)
            ->with(['user', 'campus', 'department'])
            ->get();
            
        $pdf = Pdf::loadView('school.reports.staff_pdf', compact('staffMembers', 'school'));
        return $pdf->download('staff_directory_report.pdf');
    }
}
