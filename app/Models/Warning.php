<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    protected $table = 'warnings';

    protected $fillable = ['case_id', 'warning_letter_path', 'date_issued', 'details'];
}
