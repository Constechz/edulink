<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsForumPost extends Model
{
    protected $table = 'lms_forum_posts';

    protected $fillable = [
        'forum_id',
        'parent_id',
        'user_id',
        'content',
    ];

    public function forum()
    {
        return $this->belongsTo(LmsForum::class, 'forum_id');
    }

    public function parent()
    {
        return $this->belongsTo(LmsForumPost::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
