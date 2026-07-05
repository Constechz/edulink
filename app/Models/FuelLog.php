<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    protected $table = 'fuel_logs';

    protected $fillable = ['vehicle_id', 'refuel_date', 'quantity_litres', 'cost_per_litre', 'total_cost', 'odometer_reading', 'recorded_by'];
}
