<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentRunbook extends Model
{
    protected $table = 'incident_runbooks';

    protected $fillable = ['incident_type', 'steps_to_resolve'];
}
