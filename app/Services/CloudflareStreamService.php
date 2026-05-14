<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cloudflare Stream token service.
 *
 * Uses Option 1: the /token API endpoint — no signing keys or RSA required.
 * Cloudflare generates and signs the token on their side; we just request it.
 *
 * Requirements:
 *   - CLOUDFLARE_API_TOKEN in .env  (already set)
 *   - CLOUDFLARE_ACCOUNT_ID in .env (already set)
 *
 * For signed URLs to restrict access, each video must have requireSignedURLs=true.
 * Run: php artisan stream:fix-origins   to clear domain locks on existing videos.
 * New videos uploaded after today have no domain lock (allowedOrigins removed).
 *
 * Token lifetime defaults to 2 hours (CLOUDFLARE_STREAM_TOKEN_TTL in seconds).
 */
class CloudflareStreamService
{
    private string $accountId;
    private string $apiToken;
    private int    $ttl;

    public function __construct()
    {
        $this->accountId = config('services.cloudflare.account_id', '');
        $this->apiToken  = config('services.cloudflare.api_token', '');
        $this->ttl       = (int) config('services.cloudflare.stream_token_ttl', 7200);
    }

    /**
     * Request a signed token from Cloudflare for the given video UID.
     * Returns the raw JWT string, or null on failure.
     */
    public function signedToken(string $videoId, ?int $ttl = null): ?string
    {
        $expiry = time() + ($ttl ?? $this->ttl);

        try {
            $response = Http::withToken($this->apiToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/stream/{$videoId}/token", [
                    'exp' => $expiry,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['result']['token'])) {
                return $data['result']['token'];
            }

            Log::warning('Cloudflare Stream /token failed', [
                'video_id' => $videoId,
                'status'   => $response->status(),
                'errors'   => $data['errors'] ?? $data,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Cloudflare Stream /token exception', [
                'video_id' => $videoId,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Return a signed iframe embed URL, or fall back to the plain embed URL.
     * Falls back gracefully so the video still plays even if token generation fails.
     */
    public function signedEmbedUrl(string $videoId, ?int $ttl = null): string
    {
        $token = $this->signedToken($videoId, $ttl);

        if ($token) {
            // Use the signed token as the video ID in the embed URL
            return "https://iframe.videodelivery.net/{$token}";
        }

        // Fallback: plain embed (works when video has no domain or signed-URL restriction)
        return "https://iframe.videodelivery.net/{$videoId}";
    }

    /**
     * Returns true when the minimum credentials are present.
     */
    public function isConfigured(): bool
    {
        return !empty($this->accountId) && !empty($this->apiToken);
    }

    /**
     * Extract the Cloudflare Stream video UID from an HLS or manifest URL.
     * e.g. https://customer-xxx.cloudflarestream.com/{UID}/manifest/video.m3u8
     */
    public static function extractVideoId(string $url): ?string
    {
        if (preg_match('/cloudflarestream\.com\/([a-f0-9]{32,})\//i', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Clear allowedOrigins on a single Cloudflare Stream video so it plays on any domain.
     * Cloudflare uses POST (not PATCH) to update video metadata on TUS-uploaded videos.
     */
    public function clearAllowedOrigins(string $videoId): bool
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/stream/{$videoId}", [
                    'uid'            => $videoId,
                    'allowedOrigins' => [],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('clearAllowedOrigins failed', ['video_id' => $videoId, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
