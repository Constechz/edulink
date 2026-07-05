<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HostelAllocation extends Model
{
    use BelongsToSchool;

    protected $table = 'hostel_allocations';

    protected $fillable = ['school_id', 'student_id', 'bed_id', 'academic_year_id', 'term_id', 'allocated_date', 'vacated_date'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function bed()
    {
        return $this->belongsTo(DormitoryBed::class, 'bed_id');
    }
}

