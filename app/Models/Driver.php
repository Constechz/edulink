<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Driver extends Model
{
    use BelongsToSchool;

    protected $table = 'drivers';

    protected $fillable = ['school_id', 'user_id', 'license_number', 'license_expiry'];
}
