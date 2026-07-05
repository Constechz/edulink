<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class AiFlag extends Model
{
    use BelongsToSchool;

    protected $table = 'ai_flags';

    protected $fillable = ['school_id', 'student_id', 'flag_type_id', 'severity', 'trigger_reason', 'is_resolved', 'resolution_notes'];
}
