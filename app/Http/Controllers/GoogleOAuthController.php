<?php

namespace App\Http\Controllers;

use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleOAuthController extends Controller
{
    /**
     * Redirect to Google consent screen to obtain refresh token.
     */
    public function redirect(GmailService $gmail)
    {
        try {
            $url = $gmail->getAuthorizationUrl();
            return redirect()->away($url);
        } catch (\Throwable $e) {
            Log::error('Google OAuth redirect failed', ['error' => $e->getMessage()]);
            return redirect()->to(url('/'))->with('error', 'Google OAuth not configured: ' . $e->getMessage());
        }
    }

    /**
     * Handle callback from Google; display refresh token to add to .env.
     * Never redirect from this action to avoid redirect loops.
     */
    public function callback(Request $request, GmailService $gmail)
    {
        $code = $request->query('code');
        if (empty($code)) {
            $error = $request->query('error', 'No code received');
            return response()->view('google.oauth-error', [
                'message' => 'Google OAuth failed: ' . $error,
            ], 200);
        }

        try {
            $refreshToken = $gmail->getRefreshTokenFromCode($code);
            return response()->view('google.refresh-token', [
                'refresh_token' => $refreshToken,
                'env_line' => 'GOOGLE_REFRESH_TOKEN=' . $refreshToken,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed', ['error' => $e->getMessage()]);
            return response()->view('google.oauth-error', [
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
