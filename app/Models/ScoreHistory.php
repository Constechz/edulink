<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreHistory extends Model
{
    protected $table = 'score_history';

    public $timestamps = false;

    protected $fillable = [
        'student_score_id',
        'changed_by',
        'change_type',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime',
    ];

    public function studentScore()
    {
        return $this->belongsTo(StudentScore::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
