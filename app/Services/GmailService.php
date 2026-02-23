<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;

class GmailService
{
    protected ?GoogleClient $client = null;

    protected ?Gmail $gmail = null;

    /**
     * Get or create the Google API client.
     */
    protected function getClient(): GoogleClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $client = new GoogleClient();
        $client->setApplicationName(config('app.name'));
        $client->setScopes([
            \Google\Service\Gmail::GMAIL_SEND,
            \Google\Service\Gmail::GMAIL_COMPOSE,
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $credentialsPath = config('services.google.credentials_path');
        if ($credentialsPath) {
            $path = preg_replace('#^storage/#', '', $credentialsPath);
            $fullPath = storage_path($path);
            if (file_exists($fullPath)) {
                $client->setAuthConfig($fullPath);
            }
        }
        if (!$client->getClientId()) {
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
        }

        $client->setRedirectUri(config('services.google.redirect_uri'));

        $refreshToken = config('services.google.refresh_token');
        if (empty($refreshToken)) {
            throw new \RuntimeException(
                'Google refresh token is not set. Run the OAuth flow once and set GOOGLE_REFRESH_TOKEN in .env'
            );
        }

        $client->fetchAccessTokenWithRefreshToken($refreshToken);

        $this->client = $client;
        return $client;
    }

    /**
     * Get Gmail API service instance.
     */
    protected function getGmail(): Gmail
    {
        if ($this->gmail !== null) {
            return $this->gmail;
        }
        $this->gmail = new Gmail($this->getClient());
        return $this->gmail;
    }

    /**
     * Build a MIME message and base64url encode it for Gmail API.
     */
    protected function createRawMessage(string $to, string $subject, string $body, array $options = []): string
    {
        $from = $options['from_address'] ?? config('mail.from.address');
        $fromName = $options['from_name'] ?? config('mail.from.name');
        $isHtml = $options['html'] ?? true;
        $replyTo = $options['reply_to'] ?? null;
        $cc = $options['cc'] ?? [];
        $bcc = $options['bcc'] ?? [];

        $contentType = $isHtml ? 'text/html' : 'text/plain';
        $headers = [
            'MIME-Version: 1.0',
            'From: ' . ($fromName ? '"' . addslashes($fromName) . "\" <{$from}>" : $from),
            'To: ' . $to,
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'Content-Type: ' . $contentType . '; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
        ];
        if ($replyTo) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }
        if (!empty($cc)) {
            $headers[] = 'Cc: ' . (is_array($cc) ? implode(', ', $cc) : $cc);
        }
        if (!empty($bcc)) {
            $headers[] = 'Bcc: ' . (is_array($bcc) ? implode(', ', $bcc) : $bcc);
        }

        $raw = implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($body));
        return strtr(base64_encode($raw), ['+' => '-', '/' => '_']);
    }

    /**
     * Send an email via Gmail API.
     *
     * @param string $to Recipient email address
     * @param string $subject Subject line
     * @param string $body Email body (HTML or plain text)
     * @param array $options Optional: from_address, from_name, html (bool), reply_to, cc, bcc
     * @return \Google\Service\Gmail\Message|null Sent message or null on failure
     */
    public function send(string $to, string $subject, string $body, array $options = []): ?Message
    {
        try {
            $raw = $this->createRawMessage($to, $subject, $body, $options);
            $message = new Message();
            $message->setRaw($raw);

            $result = $this->getGmail()->users_messages->send('me', $message);
            Log::info('Gmail sent', ['to' => $to, 'id' => $result->getId() ?? null]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Gmail send failed', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Send an email using Laravel Mail "from" config (convenience method).
     */
    public function sendMail(string $to, string $subject, string $body, bool $html = true): ?Message
    {
        return $this->send($to, $subject, $body, ['html' => $html]);
    }

    /**
     * Check if Gmail is configured (has refresh token and credentials).
     */
    public function isConfigured(): bool
    {
        if (empty(config('services.google.refresh_token'))) {
            return false;
        }
        $credentialsPath = config('services.google.credentials_path');
        if ($credentialsPath && file_exists(storage_path($credentialsPath))) {
            return true;
        }
        return !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret'));
    }

    /**
     * Get the authorization URL for the one-time OAuth flow (to obtain refresh token).
     */
    public function getAuthorizationUrl(): string
    {
        $client = new GoogleClient();
        $client->setApplicationName(config('app.name'));
        $client->setScopes([
            \Google\Service\Gmail::GMAIL_SEND,
            \Google\Service\Gmail::GMAIL_COMPOSE,
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setRedirectUri(config('services.google.redirect_uri'));

        $credentialsPath = config('services.google.credentials_path');
        if ($credentialsPath) {
            $path = preg_replace('#^storage/#', '', $credentialsPath);
            $fullPath = storage_path($path);
            if (file_exists($fullPath)) {
                $client->setAuthConfig($fullPath);
            }
        }
        if (!$client->getClientId()) {
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
        }

        return $client->createAuthUrl();
    }

    /**
     * Exchange authorization code for tokens and return the refresh token.
     */
    public function getRefreshTokenFromCode(string $code): string
    {
        $client = new GoogleClient();
        $client->setRedirectUri(config('services.google.redirect_uri'));

        $credentialsPath = config('services.google.credentials_path');
        if ($credentialsPath) {
            $path = preg_replace('#^storage/#', '', $credentialsPath);
            $fullPath = storage_path($path);
            if (file_exists($fullPath)) {
                $client->setAuthConfig($fullPath);
            }
        }
        if (!$client->getClientId()) {
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
        }

        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            throw new \RuntimeException('OAuth error: ' . ($token['error_description'] ?? $token['error']));
        }
        if (empty($token['refresh_token'])) {
            throw new \RuntimeException(
                'No refresh_token in response. Revoke app access at https://myaccount.google.com/permissions and try again (so Google shows consent screen).'
            );
        }
        return $token['refresh_token'];
    }
}
