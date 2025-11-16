<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'logo',
        'favicon',
        'template',
        'primary_color',
        'privacy_policy_content',
        'about_us_content',
        'footer_color',
        'footer_design',
        'copyright_text',
        'business_email',
        'banner',
        'hero_text',
        'baksh_number',
        'ngad_number',
        'rocket_number',
        'celfin_number',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the logo URL
     */
    public function getLogoAttribute()
    {
        $logo = $this->attributes['logo'] ?? null;
        return $logo ? asset('uploads/' . $this->organization_id . '/settings/logo/' . $logo) : null;
    }

    /**
     * Get the favicon URL
     */
    public function getFaviconAttribute()
    {
        $favicon = $this->attributes['favicon'] ?? null;
        return $favicon ? asset('uploads/' . $this->organization_id . '/settings/favicon/' . $favicon) : null;
    }

    /**
     * Get the banner URL
     */
    public function getBannerAttribute()
    {
        $banner = $this->attributes['banner'] ?? null;
        return $banner ? asset('uploads/' . $this->organization_id . '/settings/banner/' . $banner) : null;
    }

}
