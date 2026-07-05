<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class NotificationLog extends Model
{
    use BelongsToSchool;

    protected $table = 'notification_logs';

    protected $fillable = ['school_id', 'user_id', 'title', 'body', 'type', 'is_read'];
}
