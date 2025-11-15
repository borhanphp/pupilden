<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionContent extends Model
{
    protected $fillable = [
        'section_id',
        'block_index',
        'key',
        'value',
        'style',
    ];

    public function section()
    {
        return $this->belongsTo(PageSection::class, 'section_id');
    }

    protected $casts = [
        'style' => 'array',
    ];
}
