<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class WebsiteGalleryItem extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'image_path',
        'album_id',
        'display_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
