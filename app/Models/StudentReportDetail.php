<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StudentReportDetail extends Model
{
    use BelongsToSchool;

    protected $table = 'student_report_details';

    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'academic_year_id',
        'conduct',
        'attitude',
        'interest',
        'remarks',
        'reopening_date',
        'attendance_present',
        'attendance_total',
    ];

    protected $casts = [
        'reopening_date' => 'date',
        'attendance_present' => 'integer',
        'attendance_total' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
