<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class LmsCourse extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(LmsLesson::class, 'course_id')->orderBy('display_order');
    }
}
