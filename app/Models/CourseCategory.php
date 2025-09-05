<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'icon',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class,);
    }

    public function subCategories()
    {
        return $this->hasMany(CourseSubCategory::class);
    }
}
