<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $table = 'maintenance_logs';

    protected $fillable = ['vehicle_id', 'service_date', 'description', 'cost', 'vendor_name', 'recorded_by'];
}
