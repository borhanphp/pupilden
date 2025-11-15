<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionLayout extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'layout_config',
    ];

    public function sections()
    {
        return $this->hasMany(PageSection::class, 'layout_id');
    }

    protected $casts = [
        'layout_config' => 'array',
    ];
}
