<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'entry_date',
        'reference',
        'description',
        'created_by',
        'approved_by',
        'status',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }
}
