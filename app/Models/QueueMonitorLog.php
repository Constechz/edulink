<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMonitorLog extends Model
{
    protected $table = 'queue_monitor_logs';

    protected $fillable = ['job_class', 'status', 'execution_time_ms'];
}
