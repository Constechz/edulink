<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Vehicle extends Model
{
    use BelongsToSchool;

    protected $table = 'vehicles';

    protected $fillable = ['school_id', 'plate_number', 'model', 'capacity', 'insurance_expiry', 'roadworthy_expiry', 'status'];
}
