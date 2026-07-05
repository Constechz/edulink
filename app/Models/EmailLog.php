<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'recipient_email',
        'subject',
        'body',
        'status',
        'error_message',
    ];
}
