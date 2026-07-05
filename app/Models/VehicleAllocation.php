<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class VehicleAllocation extends Model
{
    use BelongsToSchool;

    protected $table = 'vehicle_allocations';

    protected $fillable = ['school_id', 'vehicle_id', 'route_id', 'driver_id', 'academic_year_id'];
}
