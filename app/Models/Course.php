<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'duration',
        'level',
        'language',
        'course_category_id',
        'course_sub_category_id',
        'tags',
        'keywords',
        'image',
        'price',
        'is_published',
        'is_active',
        'is_featured',
        'is_archived',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function courseSubCategory()
    {
        return $this->belongsTo(CourseSubCategory::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_students');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->orderBy('order');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the course purchases for this course
     */
    public function coursePurchases()
    {
        return $this->hasMany(CoursePurchase::class);
    }

    /**
     * Check if student is enrolled in this course
     */
    public function isEnrolledBy($studentId)
    {
        return $this->students()->where('student_id', $studentId)->exists();
    }

    /**
     * Check if student has purchased this course
     */
    public function isPurchasedBy($studentId)
    {
        return $this->coursePurchases()
            ->where('student_id', $studentId)
            ->where('payment_status', 'completed')
            ->exists();
    }
}
