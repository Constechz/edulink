<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class DataRetentionPolicy extends Model
{
    use BelongsToSchool;

    protected $table = 'data_retention_policies';

    protected $fillable = ['school_id', 'data_type', 'retention_years'];
}
