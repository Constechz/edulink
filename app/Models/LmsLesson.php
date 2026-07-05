<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function resources()
    {
        return $this->hasMany(LmsResource::class, 'lesson_id');
    }
}
