<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class LibraryCategory extends Model
{
    use BelongsToSchool;

    protected $table = 'library_categories';

    protected $fillable = ['school_id', 'name', 'description'];
}
