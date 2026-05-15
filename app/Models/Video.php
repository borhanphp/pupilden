<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'upload_status',
        'upload_progress',
        'upload_error',
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
        'upload_progress' => 'integer',
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
     * Get video thumbnail URL (Accessor)
     * Returns the best available thumbnail: custom preview > YouTube > Cloudflare > null
     */
    public function getThumbnailUrlAttribute()
    {
        // Return custom preview image if available
        if ($this->attributes['preview_image'] ?? null) {
            return Storage::disk('r2')->url($this->attributes['preview_image']);
        }
        
        if ((int)($this->attributes['video_type'] ?? 0) === 1 && ($this->attributes['video_url'] ?? null)) {
            // YouTube video thumbnail
            $videoId = $this->extractYouTubeVideoId($this->attributes['video_url']);
            if ($videoId) {
                return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }
        } elseif ((int)($this->attributes['video_type'] ?? 0) === 2 && ($this->attributes['thumbnail_url'] ?? null)) {
            // Cloudflare video thumbnail (accessing raw database value)
            return $this->attributes['thumbnail_url'];
        }
        
        return null;
    }
    
    /**
     * Get the raw Cloudflare thumbnail URL from database
     */
    public function getCloudflareThumbAttribute()
    {
        return $this->attributes['thumbnail_url'] ?? null;
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

    /**
     * Get upload status label
     */
    public function getUploadStatusLabelAttribute()
    {
        return match($this->upload_status ?? 'completed') {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            default => 'Unknown'
        };
    }

    /**
     * Get upload status badge class
     */
    public function getUploadStatusBadgeAttribute()
    {
        return match($this->upload_status ?? 'completed') {
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Check if video upload is complete
     */
    public function isUploadComplete()
    {
        return $this->upload_status === 'completed' || $this->upload_status === null;
    }

    /**
     * Check if video upload is in progress
     */
    public function isUploading()
    {
        return in_array($this->upload_status, ['pending', 'processing']);
    }

    /**
     * Check if video upload failed
     */
    public function isUploadFailed()
    {
        return $this->upload_status === 'failed';
    }
}
