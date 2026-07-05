<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class SmsDeliveryLog extends Model
{
    use BelongsToSchool;

    protected $table = 'sms_delivery_logs';

    const UPDATED_AT = null;

    protected $fillable = ['school_id', 'phone_number', 'message_body', 'credits_used', 'status', 'reference'];
}
