<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class VisitorLog extends Model
{
    use BelongsToSchool;

    protected $table = 'visitor_logs';

    protected $fillable = ['school_id', 'visitor_name', 'id_type', 'id_number', 'phone_number', 'purpose', 'whom_to_see_user_id', 'check_in_time', 'check_out_time'];
}
