<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class BackupLog extends Model
{
    use BelongsToSchool;

    protected $table = 'backup_logs';

    protected $fillable = ['school_id', 'file_path', 'file_size', 'status', 'error_message', 'completed_at'];
}
