# Gmail Service Setup – Send Email via Google OAuth

You have `google/apiclient` and credentials in `.env`. Follow these steps to send email via Gmail.

---

## 1. .env configuration

Use these variables (you already have most of them):

```env
GOOGLE_CLIENT_ID=67899099073-cqn6nih6vaupbeqm58tc45su535itu43.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI="http://localhost:8000/google/callback"
# Or use GOOGLE_REDIRECT_URL – both are read
GOOGLE_AUTH_CONFIG="storage/app/private/google/oauth-credentials.json"
GOOGLE_REFRESH_TOKEN=   # You get this in step 2
```

For production, set `GOOGLE_REDIRECT_URI` to your real callback URL, e.g.  
`https://app.pupilden.com/google/callback`, and add that exact URL in Google Cloud Console under your OAuth client’s “Authorized redirect URIs”.

---

## 2. Get the refresh token (one-time)

1. Start your app (e.g. `php artisan serve`).
2. In the browser go to: **http://localhost:8000/google/oauth**
3. Sign in with the Google account that will send the emails and allow the app.
4. You will be redirected back to a page showing a **refresh token**.
5. Copy the value and add it to `.env`:
   ```env
   GOOGLE_REFRESH_TOKEN=1//0xxxxxxxxxxxx
   ```
6. Run: `php artisan config:clear`

After this, the app can send email without opening a browser again.

---

## 3. Send email in code

Inject `GmailService` and call `send()` or `sendMail()`.

### Example 1: Controller

```php
use App\Services\GmailService;

class SomeController extends Controller
{
    public function something(GmailService $gmail)
    {
        $gmail->send(
            'recipient@example.com',
            'Subject here',
            '<p>Hello, this is <b>HTML</b> email.</p>',
            ['html' => true]
        );
        return back()->with('success', 'Email sent');
    }
}
```

### Example 2: Plain text

```php
$gmail->send(
    'user@example.com',
    'Welcome',
    'Welcome to our app!',
    ['html' => false]
);
```

### Example 3: With options

```php
$gmail->send('to@example.com', 'Subject', $body, [
    'from_address' => 'noreply@yourdomain.com',
    'from_name'    => 'Your App',
    'html'         => true,
    'reply_to'     => 'support@yourdomain.com',
    'cc'           => ['cc@example.com'],
]);
```

### Example 4: Convenience method

```php
$gmail->sendMail('user@example.com', 'Subject', '<p>Body</p>', true);
```

---

## 4. Check if Gmail is configured

```php
if (app(GmailService::class)->isConfigured()) {
    // Has refresh token and credentials
}
```

---

## 5. Files added

| File | Purpose |
|------|--------|
| `app/Services/GmailService.php` | Sends email via Gmail API, builds OAuth URL, exchanges code for refresh token |
| `app/Http/Controllers/GoogleOAuthController.php` | Routes for `/google/oauth` and `/google/callback` |
| `resources/views/google/refresh-token.blade.php` | Page that shows the refresh token after OAuth |
| `config/services.php` | `google` config (client_id, client_secret, redirect_uri, refresh_token, credentials_path) |

Routes:

- `GET /google/oauth` – redirects to Google to start OAuth
- `GET /google/callback` – Google redirects here; page shows refresh token

---

## 6. Troubleshooting

- **“Google refresh token is not set”**  
  Complete step 2 and set `GOOGLE_REFRESH_TOKEN` in `.env`, then `php artisan config:clear`.

- **“No refresh_token in response”**  
  In Google Cloud Console, ensure the OAuth consent screen is in “Testing” or “Production” and that the user is allowed. Then go to https://myaccount.google.com/permissions, remove your app’s access, and run the OAuth flow again so the consent screen is shown and a refresh token is issued.

- **Redirect URI mismatch**  
  `GOOGLE_REDIRECT_URI` (or `GOOGLE_REDIRECT_URL`) must match exactly the “Authorized redirect URIs” in your OAuth client (including `http` vs `https` and path `/google/callback`).

- **Credentials file**  
  If you use `GOOGLE_AUTH_CONFIG`, the path can be `storage/app/private/google/oauth-credentials.json` or `app/private/google/oauth-credentials.json` (relative to `storage/`). The service normalizes it.

---

## 7. Optional: use as default mailer

Laravel’s default mailer stays as in `config/mail.php`. To send all app mail through Gmail, you’d add a custom mail transport that uses `GmailService`. For now, calling `GmailService::send()` (or `sendMail()`) where you need it is enough.
