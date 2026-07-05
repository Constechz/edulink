<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class BackupSchedule extends Model
{
    use BelongsToSchool;

    protected $table = 'backup_schedules';

    protected $fillable = ['school_id', 'frequency', 'scheduled_time', 'is_active'];
}
