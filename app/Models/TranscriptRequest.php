<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class TranscriptRequest extends Model
{
    use BelongsToSchool;

    protected $table = 'transcript_requests';

    protected $fillable = ['school_id', 'student_id', 'recipient_name', 'recipient_address', 'recipient_email', 'fee_amount', 'payment_status', 'status'];
}
