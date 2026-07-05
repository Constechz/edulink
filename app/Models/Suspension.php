<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suspension extends Model
{
    protected $table = 'suspensions';

    protected $fillable = ['case_id', 'start_date', 'end_date', 'status'];
}
