<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Message extends Model
{
    use BelongsToSchool;

    protected $table = 'messages';

    protected $fillable = ['school_id', 'sender_user_id', 'channel', 'subject', 'body', 'status'];

    /**
     * Get all recipients for the message.
     */
    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class, 'message_id');
    }
}
