<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'file_name',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
