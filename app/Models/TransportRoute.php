<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class TransportRoute extends Model
{
    use BelongsToSchool;

    protected $table = 'transport_routes';

    protected $fillable = ['school_id', 'route_name', 'start_point', 'end_point'];

    public function stops()
    {
        return $this->hasMany(RouteStop::class, 'route_id');
    }

    public function getNameAttribute()
    {
        return $this->route_name;
    }
}

