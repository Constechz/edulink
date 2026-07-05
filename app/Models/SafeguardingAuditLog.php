<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafeguardingAuditLog extends Model
{
    protected $table = 'safeguarding_audit_logs';

    protected $fillable = ['case_id', 'user_id', 'action_taken'];
}
