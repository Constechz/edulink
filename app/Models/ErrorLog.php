<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $table = 'error_logs';

    protected $fillable = ['school_id', 'exception_class', 'message', 'stack_trace', 'url'];
}
