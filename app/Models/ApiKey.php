<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class ApiKey extends Model
{
    use BelongsToSchool;

    protected $table = 'api_keys';

    protected $fillable = ['school_id', 'name', 'token_hash', 'is_active', 'expires_at'];
}
