<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class LibraryReservation extends Model
{
    use BelongsToSchool;

    protected $table = 'library_reservations';

    protected $fillable = ['school_id', 'book_id', 'user_id', 'reservation_date', 'status'];
}
