<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    const UPDATED_AT = null;

    protected $fillable = ['school_id', 'user_id', 'action', 'model_type', 'model_id', 'old_values', 'new_values', 'ip_address', 'user_agent'];

    /**
     * Get the school that owns the audit log.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user that generated the audit log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
