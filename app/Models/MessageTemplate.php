<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class MessageTemplate extends Model
{
    use BelongsToSchool;

    protected $table = 'message_templates';

    protected $fillable = ['school_id', 'name', 'channel', 'subject', 'body'];
}
