<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

        $path = str_contains($this->image, '/')
            ? $this->image
            : $this->organization_id.'/sliders/'.$this->image;

        return Storage::disk('r2')->url($path);
    }
}
