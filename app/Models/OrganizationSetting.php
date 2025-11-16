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


}
