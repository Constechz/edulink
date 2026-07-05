<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteBlock extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'html_template',
        'preview_image',
        'is_dynamic',
        'dynamic_source',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_dynamic' => 'boolean',
        'is_active' => 'boolean',
    ];
}
