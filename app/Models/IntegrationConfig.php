<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class IntegrationConfig extends Model
{
    use BelongsToSchool;

    protected $table = 'integration_configs';

    protected $fillable = ['school_id', 'provider', 'credentials_encrypted', 'is_active'];
}
