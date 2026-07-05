<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffDocument;
use App\Models\StaffQualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffHrController extends Controller
{
    /**
     * Show detailed HR profile for a specific staff member.
     */
    public function show(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff access.');
        }

        $staff->load(['user', 'campus', 'department', 'documents', 'qualifications']);

        return view('school.staff_hr', compact('staff'));
    }

    /**
     * Update HR-specific details.
     */
    public function update(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff edit.');
        }

        $request->validate([
            'employment_type' => 'required|string|in:permanent,contract,temporary',
            'salary_grade' => 'nullable|string|max:100',
            'basic_salary' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'ssnit_number' => 'nullable|string|max:100',
            'tin_number' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'national_id_type' => 'nullable|string|max:100',
            'national_id_number' => 'nullable|string|max:100',
        ]);

        $staff->update($request->only([
            'employment_type',
            'salary_grade',
            'basic_salary',
            'allowances',
            'deductions',
            'contract_start',
            'contract_end',
            'bank_name',
            'bank_account',
            'bank_branch',
            'ssnit_number',
            'tin_number',
            'emergency_contact_name',
            'emergency_contact_phone',
            'national_id_type',
            'national_id_number',
        ]));

        return redirect()->back()->with('success', 'Staff HR details updated successfully.');
    }

    /**
     * Add a qualification.
     */
    public function addQualification(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff edit.');
        }

        $request->validate([
            'institution' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'year_obtained' => 'required|integer|min:1900|max:' . date('Y'),
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $certificatePath = null;
        if ($request->hasFile('certificate')) {
            $certificatePath = $request->file('certificate')->store('staff_qualifications/' . $staff->id, 'public');
        }

        StaffQualification::create([
            'staff_id' => $staff->id,
            'institution' => $request->institution,
            'qualification' => $request->qualification,
            'year_obtained' => $request->year_obtained,
            'certificate_path' => $certificatePath,
        ]);

        return redirect()->back()->with('success', 'Qualification added successfully.');
    }

    /**
     * Upload contract or other document.
     */
    public function uploadDocument(Request $request, Staff $staff)
    {
        $schoolId = $request->user()->school_id;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized staff edit.');
        }

        $request->validate([
            'document_type' => 'required|string|max:100',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'expiry_date' => 'nullable|date',
        ]);

        $filePath = $request->file('document')->store('staff_documents/' . $staff->id, 'public');

        StaffDocument::create([
            'staff_id' => $staff->id,
            'document_type' => $request->document_type,
            'file_path' => $filePath,
            'uploaded_at' => now(),
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }
}
