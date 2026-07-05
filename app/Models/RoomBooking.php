<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class RoomBooking extends Model
{
    use BelongsToSchool;

    protected $table = 'room_bookings';

    protected $fillable = ['school_id', 'room_id', 'booked_by_user_id', 'start_time', 'end_time', 'purpose'];
}
