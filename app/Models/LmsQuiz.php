<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuiz extends Model
{
    protected $table = 'lms_quizzes';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'duration_minutes',
        'passing_percentage',
    ];

    protected $casts = [
        'passing_percentage' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }

    public function questions()
    {
        return $this->hasMany(LmsQuizQuestion::class, 'quiz_id');
    }
}
