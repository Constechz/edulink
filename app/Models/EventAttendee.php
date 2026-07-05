<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    protected $table = 'event_attendees';

    protected $fillable = ['event_id', 'user_id', 'status'];
}
