<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'preview_image',
        'available_sections',
        'is_active',
    ];

    public function organizationThemes()
    {
        return $this->hasMany(OrganizationTheme::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    protected $casts = [
        'available_sections' => 'array',
    ];
}
