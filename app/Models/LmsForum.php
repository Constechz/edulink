<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsForum extends Model
{
    protected $table = 'lms_forums';

    protected $fillable = [
        'course_id',
        'title',
        'description',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function posts()
    {
        return $this->hasMany(LmsForumPost::class, 'forum_id');
    }
}
