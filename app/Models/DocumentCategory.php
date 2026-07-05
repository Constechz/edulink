<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class DocumentCategory extends Model
{
    use BelongsToSchool;

    protected $table = 'document_categories';

    protected $fillable = ['school_id', 'name', 'description'];
}
