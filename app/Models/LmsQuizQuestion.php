<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizQuestion extends Model
{
    protected $table = 'lms_quiz_questions';

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'options_json',
        'correct_answer',
        'points',
    ];

    protected $casts = [
        'options_json' => 'json',
    ];

    public function quiz()
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }
}
