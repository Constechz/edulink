<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class LibraryFine extends Model
{
    use BelongsToSchool;

    protected $table = 'library_fines';

    protected $fillable = ['school_id', 'loan_id', 'amount', 'status', 'paid_date'];
}
