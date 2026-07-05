<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Announcement extends Model
{
    use BelongsToSchool;

    protected $table = 'announcements';

    protected $fillable = ['school_id', 'title', 'content', 'target_audience', 'is_pinned', 'expires_at', 'created_by'];
}
