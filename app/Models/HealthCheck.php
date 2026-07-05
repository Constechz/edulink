<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    protected $table = 'health_checks';

    protected $fillable = ['service_name', 'status', 'response_time_ms', 'error_message', 'checked_at'];
}
