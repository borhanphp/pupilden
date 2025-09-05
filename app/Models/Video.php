<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'video_url',
        'video_type',
        'duration',
        'is_preview',
        'order',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'video_type' => 'integer',
        'duration' => 'integer',
        'is_preview' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the course that owns the video
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who created the video
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the video
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get video type label
     */
    public function getVideoTypeLabelAttribute()
    {
        return $this->video_type === 0 ? 'S3' : 'YouTube';
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return 'N/A';
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    }

    /**
     * Get video thumbnail URL (for YouTube videos)
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->video_type === 1 && $this->video_url) {
            // Extract YouTube video ID
            $videoId = $this->extractYouTubeVideoId($this->video_url);
            if ($videoId) {
                return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }
        }
        return null;
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeVideoId($url)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
