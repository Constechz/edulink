<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class PayrollPeriod extends Model
{
    use BelongsToSchool;

    protected $table = 'payroll_periods';

    protected $fillable = ['school_id', 'name', 'start_date', 'end_date', 'status'];
}
