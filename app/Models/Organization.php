<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'custom_domain',
        'address',
        'phone',
        'email',
        'website',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'tiktok',
        'pinterest',
        'logo',
        'is_active',
        'sms_active',
        'email_active',
        'whatsapp_active',
        'sms_limit',
        'email_limit',
        'whatsapp_limit',
        'plan_type',
        'status',
        'created_by',
        'updated_by'
    ];

    public function settings()
    {
        return $this->hasOne(OrganizationSetting::class);
    }
}
