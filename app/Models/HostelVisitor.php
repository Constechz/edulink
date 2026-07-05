<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HostelVisitor extends Model
{
    use BelongsToSchool;

    protected $table = 'hostel_visitors';

    protected $fillable = ['school_id', 'student_id', 'visitor_name', 'visitor_phone', 'visit_date', 'check_in_time', 'check_out_time'];
}
