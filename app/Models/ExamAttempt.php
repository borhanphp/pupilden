<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttempt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exam_id',
        'student_id',
        'score',
        'is_passed',
        'attempted_at',
        'submitted_at',
        'reviewed_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'score' => 'integer',
        'is_passed' => 'boolean',
        'attempted_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the exam that this attempt belongs to
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the student who made this attempt
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the answers for this attempt
     */
    public function answers()
    {
        return $this->hasMany(Answer::class, 'attempt_id');
    }

    /**
     * Get the user who created this attempt
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this attempt
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get total possible marks
     */
    public function getTotalMarksAttribute()
    {
        return $this->exam->questions->sum('marks');
    }

    /**
     * Get percentage score
     */
    public function getPercentageAttribute()
    {
        if ($this->total_marks == 0) {
            return 0;
        }
        return round(($this->score / $this->total_marks) * 100, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        if (!$this->submitted_at) {
            return 'bg-warning';
        }
        return $this->is_passed ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        if (!$this->submitted_at) {
            return 'In Progress';
        }
        return $this->is_passed ? 'Passed' : 'Failed';
    }

    /**
     * Mark as submitted
     */
    public function markAsSubmitted()
    {
        $this->update([
            'submitted_at' => now(),
            'reviewed_at' => now()
        ]);
    }

    /**
     * Calculate and update score
     */
    public function calculateScore()
    {
        $totalScore = $this->answers()->sum('marks_awarded');
        
        // Only update pass/fail if all answers are graded
        $totalAnswers = $this->answers()->count();
        $gradedAnswers = $this->answers()->whereNotNull('is_correct')->count();
        $allGraded = $totalAnswers === $gradedAnswers;
        
        $this->update([
            'score' => $totalScore,
            'is_passed' => $allGraded && $totalScore >= $this->exam->pass_mark
        ]);

        return $totalScore;
    }

    /**
     * Check if attempt needs manual grading
     */
    public function needsManualGrading()
    {
        return $this->answers()->whereNull('is_correct')->exists();
    }
}
