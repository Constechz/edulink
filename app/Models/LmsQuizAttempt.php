<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizAttempt extends Model
{
    protected $table = 'lms_quiz_attempts';

    public $timestamps = false;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'score',
        'is_passed',
        'attempted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_passed' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
