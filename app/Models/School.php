<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'short_name',
        'school_code',
        'logo',
        'address',
        'region',
        'district',
        'phone',
        'email',
        'website_domain',
        'custom_domain',
        'subdomain',
        'plan_id',
        'subscription_status',
        'trial_ends_at',
        'owner_name',
        'owner_email',
        'owner_phone',
        'branding',
        'settings',
        'sms_gateway_config',
        'email_config',
        'is_active',
        'onboarding_completed',
    ];

    protected $casts = [
        'branding' => 'array',
        'settings' => 'array',
        'sms_gateway_config' => 'array',
        'email_config' => 'array',
        'is_active' => 'boolean',
        'onboarding_completed' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Check if a specific module is active and enabled for the school.
     */
    public function isModuleEnabled(string $module): bool
    {
        $plan = $this->plan;
        
        // Check if module is allowed in active plan
        if (!$plan || !in_array($module, $plan->features ?? [])) {
            return false;
        }

        // Check if school administration has enabled/disabled it
        $enabledModules = $this->settings['enabled_modules'] ?? null;
        
        if (is_array($enabledModules)) {
            return in_array($module, $enabledModules);
        }

        return true; // Default to enabled if not explicitly toggled
    }

    /**
     * Check if a specific feature flag is active for the school.
     */
    public function isFeatureEnabled(string $featureKey, bool $default = false): bool
    {
        $flag = \App\Models\FeatureFlag::where('school_id', $this->id)
            ->where('feature_key', $featureKey)
            ->first();

        return $flag ? $flag->is_enabled : $default;
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function campuses()
    {
        return $this->hasMany(Campus::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    protected static function booted()
    {
        static::saved(function ($school) {
            \Illuminate\Support\Facades\Cache::forget("school_{$school->id}");
            if ($school->subdomain) {
                \Illuminate\Support\Facades\Cache::forget("school_subdomain_{$school->subdomain}");
            }
            if ($school->custom_domain) {
                \Illuminate\Support\Facades\Cache::forget("school_domain_{$school->custom_domain}");
            }
            if ($school->website_domain) {
                \Illuminate\Support\Facades\Cache::forget("school_domain_{$school->website_domain}");
            }
        });

        static::deleted(function ($school) {
            \Illuminate\Support\Facades\Cache::forget("school_{$school->id}");
            if ($school->subdomain) {
                \Illuminate\Support\Facades\Cache::forget("school_subdomain_{$school->subdomain}");
            }
            if ($school->custom_domain) {
                \Illuminate\Support\Facades\Cache::forget("school_domain_{$school->custom_domain}");
            }
            if ($school->website_domain) {
                \Illuminate\Support\Facades\Cache::forget("school_domain_{$school->website_domain}");
            }
        });
    }
}
