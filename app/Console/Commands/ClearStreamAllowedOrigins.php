<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\CloudflareStreamService;
use Illuminate\Console\Command;

class ClearStreamAllowedOrigins extends Command
{
    protected $signature   = 'stream:fix-origins';
    protected $description = 'Clear allowedOrigins domain locks on all Cloudflare Stream videos so they play on any domain';

    public function handle(CloudflareStreamService $stream): int
    {
        if (!$stream->isConfigured()) {
            $this->error('CLOUDFLARE_ACCOUNT_ID or CLOUDFLARE_API_TOKEN is not set in .env');
            return self::FAILURE;
        }

        // Find all Cloudflare videos (video_type = 2)
        $videos = Video::where('video_type', 2)->get(['id', 'title', 'cloudflare_video_id', 'video_url']);

        if ($videos->isEmpty()) {
            $this->info('No Cloudflare Stream videos found.');
            return self::SUCCESS;
        }

        $this->info("Found {$videos->count()} Cloudflare Stream video(s). Clearing allowedOrigins...");
        $this->newLine();

        $ok   = 0;
        $fail = 0;
        $skip = 0;

        foreach ($videos as $video) {
            // Prefer stored cloudflare_video_id; fall back to extracting from URL
            $cfId = $video->cloudflare_video_id
                ?: CloudflareStreamService::extractVideoId((string) $video->video_url);

            if (!$cfId) {
                $this->warn("  [{$video->id}] {$video->title} — cannot determine Cloudflare video ID, skipping");
                $skip++;
                continue;
            }

            $this->line("  [{$video->id}] {$video->title} ({$cfId})");

            $success = $stream->clearAllowedOrigins($cfId);

            if ($success) {
                $this->info("        ✓ Cleared");
                $ok++;
            } else {
                $this->warn("        ✗ Failed (check logs)");
                $fail++;
            }

            usleep(300_000); // 300 ms — avoid rate limiting
        }

        $this->newLine();
        $this->info("Done — {$ok} cleared, {$fail} failed, {$skip} skipped.");

        return $fail === 0 ? self::SUCCESS : self::FAILURE;
    }
}
