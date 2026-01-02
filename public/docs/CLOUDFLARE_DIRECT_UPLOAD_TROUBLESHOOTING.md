# Cloudflare Direct Upload Troubleshooting

## 🐛 Error: "Failed to create upload URL: Bad Request"

This error occurs when the Cloudflare Stream API rejects the direct upload request.

---

## ✅ What I Fixed

1. **Improved payload format** - Fixed `allowedOrigins` to use just domain names
2. **Added fallback** - Retries without `allowedOrigins` if first attempt fails
3. **Better error logging** - Logs full response for debugging
4. **Improved error messages** - Extracts detailed error messages from Cloudflare

---

## 🔍 Debugging Steps

### Step 1: Check Laravel Logs

Check `storage/logs/laravel.log` for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
```
[2025-12-29] Cloudflare Direct Upload Response
[2025-12-29] Cloudflare Direct Upload Failed
```

### Step 2: Verify Cloudflare Credentials

Check your `.env` file:

```env
CLOUDFLARE_API_TOKEN=your_token_here
CLOUDFLARE_ACCOUNT_ID=your_account_id_here
```

**Test credentials:**
```bash
php artisan tinker
>>> Http::withToken(env('CLOUDFLARE_API_TOKEN'))->get("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream");
```

### Step 3: Check API Token Permissions

Your Cloudflare API token needs:
- **Stream:Edit** permission
- Access to your account

**Verify in Cloudflare Dashboard:**
1. Go to "My Profile" → "API Tokens"
2. Check your token has "Stream:Edit" permission
3. Verify it's not expired

### Step 4: Test Direct Upload Endpoint

Try the API call manually:

```bash
curl -X POST "https://api.cloudflare.com/client/v4/accounts/YOUR_ACCOUNT_ID/stream/direct_upload" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "maxDurationSeconds": 3600,
    "requireSignedURLs": false
  }'
```

**Expected Response:**
```json
{
  "result": {
    "uid": "video-id-here",
    "uploadURL": "https://upload.videodelivery.net/..."
  },
  "success": true
}
```

---

## 🔧 Common Issues & Solutions

### Issue 1: Invalid API Token

**Symptoms:**
- 401 Unauthorized
- "Invalid API token"

**Solution:**
1. Regenerate API token in Cloudflare
2. Update `.env` file
3. Clear config cache: `php artisan config:clear`

### Issue 2: Wrong Account ID

**Symptoms:**
- 404 Not Found
- "Account not found"

**Solution:**
1. Get Account ID from Cloudflare Dashboard
2. Update `CLOUDFLARE_ACCOUNT_ID` in `.env`
3. Clear config cache

### Issue 3: allowedOrigins Format

**Symptoms:**
- 400 Bad Request
- "Invalid allowedOrigins"

**Solution:**
The code now automatically:
- Extracts domain from URL
- Removes protocol (http://, https://)
- Removes www. prefix
- Falls back to no allowedOrigins if it fails

**Manual fix:** Check `APP_URL` in `.env`:
```env
APP_URL=https://yourdomain.com  # Should be full URL
```

### Issue 4: API Endpoint Changed

**Symptoms:**
- 404 Not Found
- Endpoint not found

**Solution:**
Cloudflare may have changed the endpoint. Check:
- [Cloudflare Stream API Docs](https://developers.cloudflare.com/stream/)
- Current endpoint: `/stream/direct_upload`

### Issue 5: Account Doesn't Have Stream

**Symptoms:**
- 403 Forbidden
- "Stream not available"

**Solution:**
1. Verify Stream is enabled in your Cloudflare account
2. Check your plan includes Stream
3. Contact Cloudflare support if needed

---

## 🧪 Testing the Fix

### Test 1: Simple Request (No allowedOrigins)

The code now automatically retries without `allowedOrigins` if the first attempt fails.

### Test 2: Check Logs

After attempting upload, check logs:

```bash
grep "Cloudflare Direct Upload" storage/logs/laravel.log
```

You should see:
- First attempt with allowedOrigins
- Retry attempt without allowedOrigins (if first failed)
- Full response details

### Test 3: Manual API Test

Use Postman or curl to test:

```bash
curl -X POST "https://api.cloudflare.com/client/v4/accounts/YOUR_ACCOUNT_ID/stream/direct_upload" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"maxDurationSeconds": 3600, "requireSignedURLs": false}'
```

---

## 📋 Current Code Behavior

1. **First Attempt**: Tries with `allowedOrigins` (if APP_URL is set)
2. **If Fails**: Automatically retries without `allowedOrigins`
3. **Logs Everything**: Full request/response logged for debugging
4. **Better Errors**: Extracts detailed error messages

---

## 🔍 What to Check in Logs

After an upload attempt, check logs for:

```php
// Look for this in logs:
[
    'status' => 400,  // HTTP status code
    'response' => [...],  // Full Cloudflare response
    'payload' => [...]  // What we sent
]
```

**Common error patterns:**

```json
// Invalid token
{
  "errors": [{"code": 6003, "message": "Invalid API Token"}]
}

// Invalid account
{
  "errors": [{"code": 1004, "message": "Account not found"}]
}

// Invalid payload
{
  "errors": [{"code": 1000, "message": "The request was invalid"}]
}
```

---

## 💡 Alternative: Use Regular Upload

If direct upload continues to fail, the system will fall back to:
- Server-side upload for files < 5MB (based on your threshold)
- Queue-based upload for larger files

This ensures uploads still work even if direct upload has issues.

---

## 🆘 Still Having Issues?

1. **Check Cloudflare Status**: https://www.cloudflarestatus.com/
2. **Review API Documentation**: https://developers.cloudflare.com/stream/
3. **Contact Cloudflare Support**: If API token and account are correct
4. **Check Laravel Logs**: Full error details are logged

---

## ✅ Quick Checklist

- [ ] API token has "Stream:Edit" permission
- [ ] Account ID is correct
- [ ] Stream is enabled in Cloudflare account
- [ ] `.env` file has correct credentials
- [ ] Config cache cleared: `php artisan config:clear`
- [ ] Checked Laravel logs for detailed errors
- [ ] Tested API endpoint manually

---

**Last Updated**: December 29, 2025  
**Version**: 1.1 (With Fallback & Better Error Handling)

