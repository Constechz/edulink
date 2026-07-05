<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Event extends Model
{
    use BelongsToSchool;

    protected $table = 'events';

    protected $fillable = ['school_id', 'title', 'description', 'start_time', 'end_time', 'location', 'room_id'];
}
