<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseModule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'name',
        'description',
        'image',
        'status',
        'order',
        'duration',
        'duration_type',
        'duration_value',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'duration' => 'integer',
        'duration_type' => 'integer',
        'duration_value' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function files()
    {
        return $this->hasMany(CourseModuleFile::class);
    }
}
