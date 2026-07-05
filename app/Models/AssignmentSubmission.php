<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submitted_at',
        'marks_obtained',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'marks_obtained' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feedback()
    {
        return $this->hasMany(AssignmentFeedback::class, 'submission_id');
    }
}
