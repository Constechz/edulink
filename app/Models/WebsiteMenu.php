<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class WebsiteMenu extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'location',
        'name',
    ];

    public function items()
    {
        return $this->hasMany(WebsiteMenuItem::class, 'menu_id')->orderBy('display_order');
    }
}
