<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'invoice_id',
        'student_id',
        'amount',
        'payment_date',
        'method',
        'reference_number',
        'received_by',
        'receipt_number',
        'notes',
        'gateway_response',
        'is_reversed',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'gateway_response' => 'json',
        'is_reversed' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
