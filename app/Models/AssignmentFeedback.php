<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentFeedback extends Model
{
    protected $table = 'assignment_feedback';

    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'teacher_id',
        'comments',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'submission_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
