<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsResource extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lesson_id',
        'title',
        'resource_type',
        'file_path',
        'url',
    ];

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }
}
