<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class LeaveType extends Model
{
    use BelongsToSchool;

    protected $table = 'leave_types';

    protected $fillable = ['school_id', 'name', 'days_allowed'];
}
