<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'course_module_id',
        'title',
        'video_url',
        'video_type',
        'duration',
        'is_preview',
        'is_published',
        'order',
        'cloudflare_video_id',
        'thumbnail_url',
        'preview_image',
        'file_size',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'video_type' => 'integer',
        'duration' => 'integer',
        'is_preview' => 'boolean',
        'is_published' => 'boolean',
        'order' => 'integer',
        'file_size' => 'integer',
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
        return match($this->video_type) {
            0 => 'S3',
            1 => 'YouTube',
            2 => 'Cloudflare',
            default => 'Unknown'
        };
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
     * Get video thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        // Return custom preview image if available
        if ($this->preview_image) {
            return asset('storage/' . $this->preview_image);
        }
        
        if ($this->video_type === 1 && $this->video_url) {
            // YouTube video thumbnail
            $videoId = $this->extractYouTubeVideoId($this->video_url);
            if ($videoId) {
                return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }
        } elseif ($this->video_type === 2 && $this->thumbnail_url) {
            // Cloudflare video thumbnail
            return $this->thumbnail_url;
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

    /**
     * Get the course module that owns the video
     */
    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class);
    }
}
