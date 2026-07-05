<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Payslip extends Model
{
    use BelongsToSchool;

    protected $table = 'payslips';

    protected $fillable = ['school_id', 'payroll_run_id', 'staff_id', 'basic_salary', 'gross_salary', 'total_deductions', 'net_salary', 'status', 'payment_date', 'payment_method'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function items()
    {
        return $this->hasMany(PayslipItem::class, 'payslip_id');
    }
}

