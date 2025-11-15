<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationTheme extends Model
{
    protected $fillable = [
        'organization_id',
        'theme_id',
        'custom_settings',
    ];

    protected $casts = [
        'custom_settings' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
