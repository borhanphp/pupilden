<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseModuleFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_module_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'file_url',
        'file_extension',
        'file_mime_type',
        'created_by',
        'updated_by'
    ];

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class);
    }
}
