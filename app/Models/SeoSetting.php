<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_id',
        'meta_title',
        'meta_description',
        'keywords',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
