<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class LibraryLoan extends Model
{
    use BelongsToSchool;

    protected $table = 'library_loans';

    protected $fillable = ['school_id', 'book_id', 'user_id', 'loan_date', 'due_date', 'return_date', 'status'];

    public function book()
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
