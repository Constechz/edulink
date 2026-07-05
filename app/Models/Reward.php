<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Reward extends Model
{
    use BelongsToSchool;

    protected $table = 'rewards';

    protected $fillable = ['school_id', 'student_id', 'reward_type', 'description', 'date_awarded', 'recorded_by'];
}
