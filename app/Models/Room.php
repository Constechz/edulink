<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Room extends Model
{
    use BelongsToSchool;

    protected $table = 'rooms';

    protected $fillable = ['school_id', 'name', 'building', 'capacity'];
}
