<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class PayrollRun extends Model
{
    use BelongsToSchool;

    protected $table = 'payroll_runs';

    protected $fillable = ['school_id', 'payroll_period_id', 'run_date', 'run_by', 'total_gross', 'total_deductions', 'total_net'];

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}

