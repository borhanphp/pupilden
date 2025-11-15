<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'layout_id',
        'section_type',
        'title',
        'order',
        'is_active',
        'layout_config',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function layout()
    {
        return $this->belongsTo(SectionLayout::class, 'layout_id');
    }

    public function contents()
    {
        return $this->hasMany(SectionContent::class, 'section_id');
    }

    protected $casts = [
        'layout_config' => 'array',
    ];
}
