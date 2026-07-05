<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'class_id',
        'stream_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'due_date',
        'max_marks',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
