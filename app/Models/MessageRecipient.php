<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    protected $table = 'message_recipients';

    protected $fillable = ['message_id', 'recipient_user_id', 'recipient_phone', 'recipient_email', 'status', 'error_message'];

    /**
     * Get the user recipient.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
