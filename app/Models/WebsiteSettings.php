<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class WebsiteSettings extends Model
{
    use BelongsToSchool;

    protected $table = 'website_settings';

    protected $fillable = [
        'school_id',
        'site_name',
        'site_tagline',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'bg_color',
        'heading_font',
        'body_font',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_youtube',
        'google_analytics_id',
        'custom_header_scripts',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_map_embed',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
