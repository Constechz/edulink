<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\Dormitory;
use App\Models\DormitoryRoom;
use App\Models\DormitoryBed;
use App\Models\HostelAllocation;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\RouteStop;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\PayslipItem;
use App\Models\Staff;
use App\Models\HealthVisit;
use App\Models\DisciplineCase;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationsController extends Controller
{
    public function dashboard(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Metric summaries
        $booksCount = LibraryBook::where('school_id', $schoolId)->sum('copies_total');
        $activeLoansCount = LibraryLoan::where('school_id', $schoolId)->where('status', 'active')->count();
        $lowStockCount = InventoryItem::where('school_id', $schoolId)->whereColumn('quantity_in_stock', '<=', 'reorder_level')->count();
        $totalDormsCapacity = Dormitory::where('school_id', $schoolId)->sum('capacity');
        $pendingLeavesCount = LeaveRequest::where('school_id', $schoolId)->where('status', 'pending')->count();
        $disciplineCasesCount = DisciplineCase::where('school_id', $schoolId)->where('status', 'pending')->count();

        return view('school.operations.dashboard', compact(
            'booksCount',
            'activeLoansCount',
            'lowStockCount',
            'totalDormsCapacity',
            'pendingLeavesCount',
            'disciplineCasesCount'
        ));
    }

    // --- Library Module ---
    public function libraryIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $books = LibraryBook::where('school_id', $schoolId)->with(['category'])->get();
        $categories = LibraryCategory::where('school_id', $schoolId)->get();
        $activeLoans = LibraryLoan::where('school_id', $schoolId)->where('status', 'active')->with(['book', 'user'])->get();
        $students = Student::where('school_id', $schoolId)->get();

        return view('school.operations.library', compact('books', 'categories', 'activeLoans', 'students'));
    }

    public function libraryBorrow(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'book_id' => 'required|exists:library_books,id',
            'student_id' => 'required|exists:students,id',
            'due_date' => 'required|date|after:today',
        ]);

        $book = LibraryBook::where('school_id', $schoolId)->findOrFail($request->book_id);
        if ($book->copies_available <= 0) {
            return redirect()->back()->withErrors(['book_id' => 'No copies of this book are currently available.']);
        }

        // Find the user mapped to student
        $student = Student::findOrFail($request->student_id);
        $user = User::where('school_id', $schoolId)->where('name', $student->first_name . ' ' . $student->last_name)->first();
        if (!$user) {
            $user = $request->user(); // Fallback to current staff
        }

        DB::transaction(function() use ($schoolId, $request, $book, $user) {
            LibraryLoan::create([
                'school_id' => $schoolId,
                'book_id' => $book->id,
                'user_id' => $user->id,
                'loan_date' => now(),
                'due_date' => $request->due_date,
                'status' => 'active'
            ]);

            $book->decrement('copies_available');
        });

        return redirect()->back()->with('success', 'Book issued successfully.');
    }

    public function libraryReturn(Request $request, LibraryLoan $loan)
    {
        $schoolId = $request->user()->school_id;
        if ($loan->school_id !== $schoolId) {
            abort(403);
        }

        DB::transaction(function() use ($loan) {
            $loan->update([
                'return_date' => now(),
                'status' => 'returned'
            ]);

            $loan->book->increment('copies_available');
        });

        return redirect()->back()->with('success', 'Book returned successfully.');
    }

    // --- Inventory Module ---
    public function inventoryIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $items = InventoryItem::where('school_id', $schoolId)->with(['category'])->get();
        $categories = InventoryCategory::where('school_id', $schoolId)->get();
        $transactions = StockTransaction::where('school_id', $schoolId)->with(['item', 'recorder'])->orderBy('transaction_date', 'desc')->get();

        return view('school.operations.inventory', compact('items', 'categories', 'transactions'));
    }

    public function inventoryTransaction(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = InventoryItem::where('school_id', $schoolId)->findOrFail($request->inventory_item_id);

        if ($request->type === 'out' && $item->quantity_in_stock < $request->quantity) {
            return redirect()->back()->withErrors(['quantity' => 'Insufficient stock for this issuance.']);
        }

        DB::transaction(function() use ($schoolId, $request, $item) {
            StockTransaction::create([
                'school_id' => $schoolId,
                'inventory_item_id' => $item->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'transaction_date' => now(),
                'recorded_by' => $request->user()->id,
                'notes' => $request->notes
            ]);

            if ($request->type === 'in') {
                $item->increment('quantity_in_stock', $request->quantity);
            } elseif ($request->type === 'out') {
                $item->decrement('quantity_in_stock', $request->quantity);
            } else {
                $item->update(['quantity_in_stock' => $request->quantity]);
            }
        });

        return redirect()->back()->with('success', 'Stock transaction recorded.');
    }

    // --- Hostel Module ---
    public function hostelIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $dormitories = Dormitory::where('school_id', $schoolId)->get();
        $students = Student::where('school_id', $schoolId)->get();
        
        $activeAllocations = HostelAllocation::where('school_id', $schoolId)
            ->whereNull('vacated_date')
            ->with(['student', 'bed.room.dormitory'])
            ->get();

        return view('school.operations.hostel', compact('dormitories', 'students', 'activeAllocations'));
    }

    public function hostelAllocate(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'dormitory_id' => 'required|exists:dormitories,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $dorm = Dormitory::where('school_id', $schoolId)->findOrFail($request->dormitory_id);

        // Find an unoccupied bed in this dormitory
        $bed = DormitoryBed::whereHas('room', function($q) use ($dorm) {
            $q->where('dormitory_id', $dorm->id);
        })->where('is_occupied', false)->first();

        if (!$bed) {
            return redirect()->back()->withErrors(['dormitory_id' => 'No vacant beds in this dormitory room.']);
        }

        // Active Academic Year
        $activeYear = DB::table('academic_years')->where('school_id', $schoolId)->where('is_current', true)->first()
            ?: DB::table('academic_years')->where('school_id', $schoolId)->first();

        if (!$activeYear) {
            return redirect()->back()->withErrors(['dormitory_id' => 'Please configure an Academic Year context first.']);
        }

        DB::transaction(function() use ($schoolId, $request, $bed, $activeYear) {
            HostelAllocation::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'bed_id' => $bed->id,
                'academic_year_id' => $activeYear->id,
                'allocated_date' => now(),
            ]);

            $bed->update(['is_occupied' => true]);
        });

        return redirect()->back()->with('success', 'Student allocated to dormitory bed.');
    }

    public function dormitoryStore(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'name' => 'required|string|max:255',
            'gender_allowed' => 'required|string|in:Male,Female,Mixed',
            'capacity' => 'required|integer|min:1|max:500',
        ]);

        DB::transaction(function() use ($schoolId, $request) {
            $dormitory = Dormitory::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'gender_allowed' => $request->gender_allowed,
                'capacity' => $request->capacity,
            ]);

            $room = DormitoryRoom::create([
                'dormitory_id' => $dormitory->id,
                'room_number' => 'Gen-Room',
                'capacity' => $request->capacity,
            ]);

            for ($i = 1; $i <= $request->capacity; $i++) {
                DormitoryBed::create([
                    'room_id' => $room->id,
                    'bed_number' => 'B-' . sprintf('%02d', $i),
                    'is_occupied' => false,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Dormitory block created successfully with automated bed allocations.');
    }

    public function dormitoryUpdate(Request $request, Dormitory $dormitory)
    {
        $schoolId = $request->user()->school_id;
        if ($dormitory->school_id !== $schoolId) {
            abort(403, 'Unauthorized dormitory edit.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'gender_allowed' => 'required|string|in:Male,Female,Mixed',
        ]);

        $dormitory->update([
            'name' => $request->name,
            'gender_allowed' => $request->gender_allowed,
        ]);

        return redirect()->back()->with('success', 'Dormitory block details updated successfully.');
    }

    public function dormitoryDestroy(Request $request, Dormitory $dormitory)
    {
        $schoolId = $request->user()->school_id;
        if ($dormitory->school_id !== $schoolId) {
            abort(403, 'Unauthorized dormitory delete.');
        }

        DB::transaction(function() use ($dormitory) {
            $rooms = DormitoryRoom::where('dormitory_id', $dormitory->id)->get();
            foreach ($rooms as $room) {
                $bedIds = DormitoryBed::where('room_id', $room->id)->pluck('id');
                HostelAllocation::whereIn('bed_id', $bedIds)->delete();
                DormitoryBed::where('room_id', $room->id)->delete();
            }
            DormitoryRoom::where('dormitory_id', $dormitory->id)->delete();
            $dormitory->delete();
        });

        return redirect()->back()->with('success', 'Dormitory block and all associated records deleted successfully.');
    }

    public function hostelDeallocate(Request $request, HostelAllocation $allocation)
    {
        $schoolId = $request->user()->school_id;
        if ($allocation->school_id !== $schoolId) {
            abort(403, 'Unauthorized deallocation.');
        }

        DB::transaction(function() use ($allocation) {
            $allocation->update([
                'vacated_date' => now(),
            ]);

            $bed = $allocation->bed;
            if ($bed) {
                $bed->update(['is_occupied' => false]);
            }
        });

        return redirect()->back()->with('success', 'Student deallocated (vacated) from dormitory successfully.');
    }

    // --- Transport Module ---
    public function transportIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $routes = TransportRoute::where('school_id', $schoolId)->with(['stops'])->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->get();

        return view('school.operations.transport', compact('routes', 'vehicles'));
    }

    public function transportRouteStore(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'route_name' => 'required|string|max:255',
            'start_point' => 'required|string|max:255',
            'end_point' => 'required|string|max:255',
        ]);

        TransportRoute::create([
            'school_id' => $schoolId,
            'route_name' => $request->route_name,
            'start_point' => $request->start_point,
            'end_point' => $request->end_point,
        ]);

        return redirect()->back()->with('success', 'Transport route created successfully.');
    }

    public function transportStopStore(Request $request, TransportRoute $route)
    {
        $schoolId = $request->user()->school_id;
        if ($route->school_id !== $schoolId) {
            abort(403);
        }

        $request->validate([
            'stop_name' => 'required|string|max:255',
            'pickup_time' => 'required',
            'dropoff_time' => 'required',
        ]);

        RouteStop::create([
            'route_id' => $route->id,
            'stop_name' => $request->stop_name,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
        ]);

        return redirect()->back()->with('success', 'Transit stop schedule added successfully.');
    }

    public function vehicleStore(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'plate_number' => 'required|string|max:50',
            'model' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string|in:active,maintenance,inactive',
        ]);

        Vehicle::create([
            'school_id' => $schoolId,
            'plate_number' => $request->plate_number,
            'model' => $request->model,
            'capacity' => $request->capacity,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Vehicle registered successfully in the fleet.');
    }

    public function transportRouteDelete(Request $request, TransportRoute $route)
    {
        $schoolId = $request->user()->school_id;
        if ($route->school_id !== $schoolId) {
            abort(403);
        }

        $route->stops()->delete();
        $route->delete();

        return redirect()->back()->with('success', 'Transport route deleted successfully.');
    }

    public function vehicleDelete(Request $request, Vehicle $vehicle)
    {
        $schoolId = $request->user()->school_id;
        if ($vehicle->school_id !== $schoolId) {
            abort(403);
        }

        $vehicle->delete();

        return redirect()->back()->with('success', 'Vehicle removed from the fleet.');
    }

    // --- HR & Payroll ---
    public function hrIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $leaveTypes = LeaveType::where('school_id', $schoolId)->get();
        $leaveRequests = LeaveRequest::where('school_id', $schoolId)->with(['staff.user', 'leaveType'])->get();
        $staff = Staff::where('school_id', $schoolId)->with(['user'])->get();
        $payrollPeriods = PayrollPeriod::where('school_id', $schoolId)->get();
        $payslips = Payslip::where('school_id', $schoolId)->with(['staff.user', 'payrollRun.period'])->get();

        return view('school.operations.hr', compact('leaveTypes', 'leaveRequests', 'staff', 'payrollPeriods', 'payslips'));
    }

    public function hrLeaveApply(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        LeaveRequest::create([
            'school_id' => $schoolId,
            'staff_id' => $request->staff_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Leave request logged successfully.');
    }

    public function hrPayrollRun(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
        ]);

        $period = PayrollPeriod::where('school_id', $schoolId)->findOrFail($request->payroll_period_id);
        if ($period->status === 'closed') {
            return redirect()->back()->withErrors(['payroll_period_id' => 'This payroll period is already finalized/closed.']);
        }

        $staffList = Staff::where('school_id', $schoolId)->get();

        DB::transaction(function() use ($schoolId, $request, $period, $staffList) {
            $payrollRun = PayrollRun::create([
                'school_id' => $schoolId,
                'payroll_period_id' => $period->id,
                'run_date' => now(),
                'run_by' => $request->user()->id,
                'total_gross' => 0,
                'total_deductions' => 0,
                'total_net' => 0,
            ]);

            $totalGross = 0;
            $totalDeductions = 0;
            $totalNet = 0;

            foreach ($staffList as $staff) {
                // Read dynamic values from staff profile
                $basic = (float) ($staff->basic_salary ?? 0.00);
                $allowances = (float) ($staff->allowances ?? 0.00);
                $deductions = (float) ($staff->deductions ?? 0.00);
                
                $gross = $basic + $allowances;
                $net = $gross - $deductions;

                $payslip = Payslip::create([
                    'school_id' => $schoolId,
                    'payroll_run_id' => $payrollRun->id,
                    'staff_id' => $staff->id,
                    'basic_salary' => $basic,
                    'gross_salary' => $gross,
                    'total_deductions' => $deductions,
                    'net_salary' => $net,
                    'status' => 'paid',
                    'payment_date' => now(),
                    'payment_method' => 'bank_transfer',
                ]);

                if ($allowances > 0) {
                    PayslipItem::create([
                        'payslip_id' => $payslip->id,
                        'name' => 'Transport Allowance',
                        'type' => 'allowance',
                        'amount' => $allowances
                    ]);
                }

                if ($deductions > 0) {
                    PayslipItem::create([
                        'payslip_id' => $payslip->id,
                        'name' => 'SSNIT Deductible',
                        'type' => 'deduction',
                        'amount' => $deductions
                    ]);
                }

                $totalGross += $gross;
                $totalDeductions += $deductions;
                $totalNet += $net;
            }

            $payrollRun->update([
                'total_gross' => $totalGross,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet
            ]);

            $period->update(['status' => 'closed']);
        });

        return redirect()->back()->with('success', 'Payroll calculated and payslips generated for this period.');
    }

    public function hrPayslipShow(Request $request, Payslip $payslip)
    {
        if ($payslip->school_id !== $request->user()->school_id) {
            abort(403);
        }

        $payslip->load(['staff.user', 'payrollRun.period', 'items']);
        return view('school.operations.payslip', compact('payslip'));
    }

    // --- Health & Discipline ---
    public function healthDisciplineIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $students = Student::where('school_id', $schoolId)->get();
        $healthVisits = HealthVisit::where('school_id', $schoolId)->with(['student', 'recorder'])->orderBy('visit_date', 'desc')->get();
        $disciplineCases = DisciplineCase::where('school_id', $schoolId)->with(['student', 'reporter'])->orderBy('incident_date', 'desc')->get();

        return view('school.operations.health_discipline', compact('students', 'healthVisits', 'disciplineCases'));
    }

    public function healthVisitStore(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'visit_date' => 'required|date',
            'symptoms' => 'required|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
        ]);

        HealthVisit::create([
            'school_id' => $schoolId,
            'student_id' => $request->student_id,
            'visit_date' => $request->visit_date,
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'recorded_by' => $request->user()->id
        ]);

        return redirect()->back()->with('success', 'Health visit logged.');
    }

    public function disciplineCaseStore(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'incident_date' => 'required|date',
            'category' => 'required|in:minor,major,critical',
            'description' => 'required|string',
        ]);

        DisciplineCase::create([
            'school_id' => $schoolId,
            'student_id' => $request->student_id,
            'incident_date' => $request->incident_date,
            'category' => $request->category,
            'description' => $request->description,
            'status' => 'pending',
            'reported_by' => $request->user()->id
        ]);

        return redirect()->back()->with('success', 'Discipline incident logged.');
    }
}
