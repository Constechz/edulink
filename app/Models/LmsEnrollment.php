<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsEnrollment extends Model
{
    protected $table = 'lms_enrollments';

    protected $fillable = [
        'course_id',
        'student_id',
        'enrolled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
