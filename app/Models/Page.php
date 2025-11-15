<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'slug',
        'type',
        'is_active',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function sections()
    {
        return $this->hasMany(PageSection::class);
    }

}
