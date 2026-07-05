<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    protected $fillable = [
        'staff_id',
        'document_type',
        'file_path',
        'uploaded_at',
        'expiry_date',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'expiry_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
