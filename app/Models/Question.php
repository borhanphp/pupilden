<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'options',
        'correct_answer',
        'marks',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'options' => 'array',
        'marks' => 'integer',
    ];

    /**
     * Get the exam that owns the question
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user who created the question
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the question
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the answers for this question
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get question type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'mcq' => 'Multiple Choice',
            'short_answer' => 'Short Answer',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    /**
     * Get question type badge class
     */
    public function getTypeBadgeClassAttribute()
    {
        return match($this->type) {
            'mcq' => 'bg-primary',
            'short_answer' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get formatted options for display
     */
    public function getFormattedOptionsAttribute()
    {
        if ($this->type === 'mcq' && $this->options) {
            return collect($this->options)->map(function($option, $index) {
                return [
                    'key' => chr(65 + $index), // A, B, C, D
                    'value' => $option
                ];
            });
        }
        return collect();
    }

    /**
     * Get correct answer display
     */
    public function getCorrectAnswerDisplayAttribute()
    {
        if ($this->type === 'mcq' && $this->options && $this->correct_answer) {
            $index = array_search($this->correct_answer, $this->options);
            if ($index !== false) {
                return chr(65 + $index) . '. ' . $this->correct_answer;
            }
        }
        return $this->correct_answer;
    }

    /**
     * Check if question has options
     */
    public function getHasOptionsAttribute()
    {
        return $this->type === 'mcq' && !empty($this->options);
    }

    /**
     * Get total answers count
     */
    public function getAnswersCountAttribute()
    {
        return $this->answers()->count();
    }
}
