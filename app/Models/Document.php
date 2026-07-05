<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'documents';

    protected $fillable = ['school_id', 'category_id', 'title', 'description', 'current_file_path', 'mime_type', 'owner_id', 'is_public'];
}
