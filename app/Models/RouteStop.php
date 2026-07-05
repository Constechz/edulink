<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    protected $table = 'route_stops';

    protected $fillable = ['route_id', 'stop_name', 'pickup_time', 'dropoff_time', 'display_order'];
}
