<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class MentoringMatch extends Model
{
    use BelongsToSchool;

    protected $table = 'mentoring_matches';

    protected $fillable = ['school_id', 'mentor_alumni_id', 'student_id', 'start_date', 'end_date', 'status'];
}
