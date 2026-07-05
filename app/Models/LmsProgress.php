<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsProgress extends Model
{
    protected $table = 'lms_progress';

    protected $fillable = [
        'student_id',
        'lesson_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }
}
