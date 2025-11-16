<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    protected $table = 'course_students';

    protected $fillable = [
        'course_id',
        'student_id',
        'status',
        'rejection_reason',
        'enrolled_at',
        'is_completed',
        'completed_at',
        'certificate_id',
        'approved_at',
        'disapproved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'disapproved_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isDisapproved()
    {
        return $this->status === 'disapproved';
    }
}
