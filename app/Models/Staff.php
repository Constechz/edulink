<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'campus_id',
        'staff_number',
        'designation',
        'department_id',
        'employment_type',
        'qualification',
        'specialization',
        'professional_cert',
        'date_joined',
        'date_left',
        'contract_start',
        'contract_end',
        'salary_grade',
        'basic_salary',
        'allowances',
        'deductions',
        'bank_name',
        'bank_account',
        'bank_branch',
        'ssnit_number',
        'tin_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'national_id_type',
        'national_id_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_joined' => 'date',
        'date_left' => 'date',
        'contract_start' => 'date',
        'contract_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function documents()
    {
        return $this->hasMany(StaffDocument::class);
    }

    public function qualifications()
    {
        return $this->hasMany(StaffQualification::class);
    }

    public function appraisals()
    {
        return $this->hasMany(StaffAppraisal::class);
    }
}
