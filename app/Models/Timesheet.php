<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Timesheet extends Model
{
    use BelongsToSchool;

    protected $table = 'timesheets';

    protected $fillable = ['school_id', 'staff_id', 'date', 'clock_in', 'clock_out', 'status'];
}
