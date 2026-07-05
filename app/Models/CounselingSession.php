<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class CounselingSession extends Model
{
    use BelongsToSchool;

    protected $table = 'counseling_sessions';

    protected $fillable = ['school_id', 'student_id', 'counselor_user_id', 'session_date', 'notes', 'follow_up_required'];
}
