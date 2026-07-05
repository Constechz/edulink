<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessLog extends Model
{
    protected $table = 'document_access_logs';

    protected $fillable = ['document_id', 'user_id', 'action_taken', 'accessed_at'];
}
