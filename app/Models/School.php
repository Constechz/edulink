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
        static::created(function ($school) {
            try {
                $school->seedDefaultGradingScales();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to seed default grading scales for school {$school->id}: " . $e->getMessage());
            }
        });

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

    /**
     * Seed default grading scales for this school.
     */
    public function seedDefaultGradingScales(): void
    {
        $schoolId = $this->id;

        // 1. Creche & KG Standards-Based Scale
        $kgScale = \App\Models\GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'GES KG Standards-Based', 'level' => 'KG'],
            ['is_active' => true, 'is_default' => false]
        );

        $sbcItems = [
            ['grade' => 'Adv', 'min_score' => 80.00, 'max_score' => 100.00, 'grade_point' => 4.00, 'description' => 'Advanced', 'display_order' => 1],
            ['grade' => 'Prof', 'min_score' => 75.00, 'max_score' => 79.99, 'grade_point' => 3.00, 'description' => 'Proficient', 'display_order' => 2],
            ['grade' => 'Appr', 'min_score' => 70.00, 'max_score' => 74.99, 'grade_point' => 2.00, 'description' => 'Approaching Proficiency', 'display_order' => 3],
            ['grade' => 'Dev', 'min_score' => 65.00, 'max_score' => 69.99, 'grade_point' => 1.00, 'description' => 'Developing', 'display_order' => 4],
            ['grade' => 'Beg', 'min_score' => 0.00, 'max_score' => 64.99, 'grade_point' => 0.00, 'description' => 'Beginning', 'display_order' => 5],
        ];

        foreach ($sbcItems as $item) {
            \App\Models\GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $kgScale->id, 'grade' => $item['grade']],
                $item
            );
        }

        // 2. Primary Standards-Based Scale
        $primaryScale = \App\Models\GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'GES Primary Standards-Based', 'level' => 'Primary'],
            ['is_active' => true, 'is_default' => false]
        );

        foreach ($sbcItems as $item) {
            \App\Models\GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $primaryScale->id, 'grade' => $item['grade']],
                $item
            );
        }

        // 3. GES Basic School Scale (JHS)
        $basicScale = \App\Models\GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'GES JHS BECE Scale', 'level' => 'JHS'],
            ['is_active' => true, 'is_default' => true]
        );

        $basicItems = [
            ['grade' => '1', 'min_score' => 80.00, 'max_score' => 100.00, 'grade_point' => 1.00, 'description' => 'Highest/Excellent', 'display_order' => 1],
            ['grade' => '2', 'min_score' => 70.00, 'max_score' => 79.99, 'grade_point' => 2.00, 'description' => 'Very Good', 'display_order' => 2],
            ['grade' => '3', 'min_score' => 60.00, 'max_score' => 69.99, 'grade_point' => 3.00, 'description' => 'Good', 'display_order' => 3],
            ['grade' => '4', 'min_score' => 55.00, 'max_score' => 59.99, 'grade_point' => 4.00, 'description' => 'High Credit', 'display_order' => 4],
            ['grade' => '5', 'min_score' => 50.00, 'max_score' => 54.99, 'grade_point' => 5.00, 'description' => 'Credit', 'display_order' => 5],
            ['grade' => '6', 'min_score' => 45.00, 'max_score' => 49.99, 'grade_point' => 6.00, 'description' => 'Pass', 'display_order' => 6],
            ['grade' => '7', 'min_score' => 40.00, 'max_score' => 44.99, 'grade_point' => 7.00, 'description' => 'Pass', 'display_order' => 7],
            ['grade' => '8', 'min_score' => 35.00, 'max_score' => 39.99, 'grade_point' => 8.00, 'description' => 'Pass', 'display_order' => 8],
            ['grade' => '9', 'min_score' => 0.00, 'max_score' => 34.99, 'grade_point' => 9.00, 'description' => 'Fail', 'display_order' => 9],
        ];

        foreach ($basicItems as $item) {
            \App\Models\GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $basicScale->id, 'grade' => $item['grade']],
                $item
            );
        }

        // 4. WAEC SHS Scale
        $shsScale = \App\Models\GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'WAEC SHS Standard', 'level' => 'SHS'],
            ['is_active' => true, 'is_default' => false]
        );

        $shsItems = [
            ['grade' => 'A1', 'min_score' => 75.00, 'max_score' => 100.00, 'grade_point' => 1.00, 'description' => 'Excellent', 'display_order' => 1],
            ['grade' => 'B2', 'min_score' => 70.00, 'max_score' => 74.99, 'grade_point' => 2.00, 'description' => 'Very Good', 'display_order' => 2],
            ['grade' => 'B3', 'min_score' => 65.00, 'max_score' => 69.99, 'grade_point' => 3.00, 'description' => 'Good', 'display_order' => 3],
            ['grade' => 'C4', 'min_score' => 60.00, 'max_score' => 64.99, 'grade_point' => 4.00, 'description' => 'Credit', 'display_order' => 4],
            ['grade' => 'C5', 'min_score' => 55.00, 'max_score' => 59.99, 'grade_point' => 5.00, 'description' => 'Credit', 'display_order' => 5],
            ['grade' => 'C6', 'min_score' => 50.00, 'max_score' => 54.99, 'grade_point' => 6.00, 'description' => 'Credit', 'display_order' => 6],
            ['grade' => 'D7', 'min_score' => 45.00, 'max_score' => 49.99, 'grade_point' => 7.00, 'description' => 'Pass', 'display_order' => 7],
            ['grade' => 'E8', 'min_score' => 40.00, 'max_score' => 44.99, 'grade_point' => 8.00, 'description' => 'Pass', 'display_order' => 8],
            ['grade' => 'F9', 'min_score' => 0.00, 'max_score' => 39.99, 'grade_point' => 9.00, 'description' => 'Fail', 'display_order' => 9],
        ];

        foreach ($shsItems as $item) {
            \App\Models\GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $shsScale->id, 'grade' => $item['grade']],
                $item
            );
        }
    }
}
