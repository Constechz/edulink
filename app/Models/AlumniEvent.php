<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class AlumniEvent extends Model
{
    use BelongsToSchool;

    protected $table = 'alumni_events';

    protected $fillable = ['school_id', 'title', 'description', 'event_date', 'venue'];
}
