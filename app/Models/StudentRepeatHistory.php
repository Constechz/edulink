<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StudentRepeatHistory extends Model
{
    use BelongsToSchool;

    protected $table = 'student_repeat_history';

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'academic_year_id',
        'repeat_count_at_this_class',
        'reason',
        'recorded_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
