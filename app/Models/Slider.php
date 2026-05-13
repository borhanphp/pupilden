<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'link',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        // Legacy: full path under storage (e.g. sliders/{org}/random-name.jpg)
        if (str_contains($this->image, '/')) {
            return asset('uploads/'.$this->image);
        }

        // Same pattern as Course images: filename only + org folder
        return asset('uploads/'.$this->organization_id.'/sliders/'.$this->image);
    }
}
