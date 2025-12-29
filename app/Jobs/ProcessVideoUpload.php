<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoUpload implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 3600; // 1 hour for large files

    /**
     * The video model instance.
     */
    protected $video;

    /**
     * The temporary file path.
     */
    protected $tempFilePath;

    /**
     * The original file name.
     */
    protected $originalFileName;

    /**
     * The file size.
     */
    protected $fileSize;

    /**
     * Create a new job instance.
     */
    public function __construct(Video $video, string $tempFilePath, string $originalFileName, int $fileSize)
    {
        $this->video = $video;
        $this->tempFilePath = $tempFilePath;
        $this->originalFileName = $originalFileName;
        $this->fileSize = $fileSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting video upload for Video ID: {$this->video->id}");

            // Update video status to processing
            $this->video->update([
                'upload_status' => 'processing',
                'upload_progress' => 0
            ]);

            // Upload to Cloudflare
            $cloudflareData = $this->uploadToCloudflare();

            if ($cloudflareData['success']) {
                // Update video with Cloudflare data
                $this->video->update([
                    'video_url' => $cloudflareData['video_url'],
                    'cloudflare_video_id' => $cloudflareData['video_id'],
                    'thumbnail_url' => $cloudflareData['thumbnail_url'],
                    'file_size' => $this->fileSize,
                    'duration' => $cloudflareData['duration'] ?? $this->video->duration,
                    'upload_status' => 'completed',
                    'upload_progress' => 100
                ]);

                Log::info("Video upload completed successfully for Video ID: {$this->video->id}");
            } else {
                throw new \Exception($cloudflareData['message']);
            }

            // Clean up temporary file
            if (Storage::exists($this->tempFilePath)) {
                Storage::delete($this->tempFilePath);
            }

        } catch (\Exception $e) {
            Log::error("Video upload failed for Video ID: {$this->video->id}. Error: " . $e->getMessage());
            
            // Update video status to failed
            $this->video->update([
                'upload_status' => 'failed',
                'upload_error' => $e->getMessage()
            ]);

            // Clean up temporary file
            if (Storage::exists($this->tempFilePath)) {
                Storage::delete($this->tempFilePath);
            }

            throw $e;
        }
    }

    /**
     * Upload video to Cloudflare Stream
     */
    private function uploadToCloudflare()
    {
        try {
            // Check if Cloudflare credentials are configured
            if (!env('CLOUDFLARE_API_TOKEN') || !env('CLOUDFLARE_ACCOUNT_ID')) {
                return [
                    'success' => false,
                    'message' => 'Cloudflare credentials not configured'
                ];
            }

            // Get the full storage path
            $fullPath = Storage::path($this->tempFilePath);

            // Verify the file exists
            if (!file_exists($fullPath)) {
                return [
                    'success' => false,
                    'message' => 'Temporary file not found at: ' . $fullPath
                ];
            }

            Log::info("Uploading to Cloudflare: {$fullPath}");

            // Prepare Cloudflare Stream API request with increased timeout
            $response = Http::timeout(3600) // 1 hour timeout for large files
                ->withToken(env('CLOUDFLARE_API_TOKEN'))
                ->attach('file', fopen($fullPath, 'r'), $this->originalFileName)
                ->post("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream", [
                    'name' => $this->video->title,
                    'requireSignedURLs' => false,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['result'])) {
                $result = $data['result'];
                
                return [
                    'success' => true,
                    'video_id' => $result['uid'],
                    'video_url' => $result['playback']['hls'],
                    'thumbnail_url' => $result['thumbnail'],
                    'duration' => isset($result['duration']) ? (int)$result['duration'] : null,
                    'message' => 'Video uploaded successfully to Cloudflare'
                ];
            } else {
                $errorMessage = isset($data['errors']) ? implode(', ', array_column($data['errors'], 'message')) : 'Unknown error';
                return [
                    'success' => false,
                    'message' => 'Cloudflare upload failed: ' . $errorMessage
                ];
            }

        } catch (\Exception $e) {
            Log::error('Cloudflare upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Upload error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Video upload job failed for Video ID: {$this->video->id}. Exception: " . $exception->getMessage());
        
        // Update video status to failed
        $this->video->update([
            'upload_status' => 'failed',
            'upload_error' => $exception->getMessage()
        ]);

        // Clean up temporary file
        if (Storage::exists($this->tempFilePath)) {
            Storage::delete($this->tempFilePath);
        }
    }
}
