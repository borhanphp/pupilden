<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'type',
        'pass_mark',
        'is_published',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'pass_mark' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Get the course that owns the exam
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who created the exam
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the exam
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the questions for this exam
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get exam attempts for this exam
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    

    /**
     * Get exam type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'pre_course' => 'Pre-Course Assessment',
            'final_exam' => 'Final Exam',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    /**
     * Get exam status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_published ? 'bg-success' : 'bg-secondary';
    }

    /**
     * Get exam status text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_published ? 'Published' : 'Draft';
    }

    /**
     * Get total questions count
     */
    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    /**
     * Get total attempts count
     */
    public function getAttemptsCountAttribute()
    {
        return $this->attempts()->count();
    }
}
