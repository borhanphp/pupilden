<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_text',
        'is_correct',
        'marks_awarded',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_awarded' => 'integer',
    ];

    /**
     * Get the exam attempt this answer belongs to
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    /**
     * Get the question this answer is for
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the user who created this answer
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this answer
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
