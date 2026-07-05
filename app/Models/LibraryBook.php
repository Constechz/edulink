<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBook extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'library_books';

    protected $fillable = ['school_id', 'category_id', 'title', 'author', 'isbn', 'publisher', 'published_year', 'copies_total', 'copies_available', 'location_rack'];

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }
}
