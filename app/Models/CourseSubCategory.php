<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSubCategory extends Model
{
    protected $fillable = [
        'organization_id',
        'course_category_id',
        'name',
        'slug',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
