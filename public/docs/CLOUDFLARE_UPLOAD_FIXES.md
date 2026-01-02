# Cloudflare Upload Fixes - Video Name & Large File Issues

## 🐛 Issues Fixed

### Issue 1: Video Name Not Sent to Cloudflare
**Problem**: Video title/name was not being included in Cloudflare upload request.

**Fix**: Added video metadata to the direct upload URL request:
```php
$payload['meta'] = [
    'name' => $request->title
];
```

### Issue 2: 400 Error for Large Videos
**Problem**: Large videos (>500MB) were getting 400 Bad Request errors.

**Fixes Applied**:
1. **Removed allowedOrigins for large files** - This was causing 400 errors
2. **Increased timeout** - Set to 2-4 hours for large files
3. **Better error handling** - Shows actual Cloudflare error messages
4. **Improved upload process** - Better handling of upload response

---

## ✅ What Was Changed

### 1. Controller (`VideoController.php`)

**Added video metadata:**
```php
if ($request->has('title') && !empty($request->title)) {
    $payload['meta'] = [
        'name' => $request->title
    ];
}
```

**Improved large file handling:**
- Removes `allowedOrigins` for files > 500MB (prevents 400 errors)
- Increases `maxDurationSeconds` to 4 hours for very large files
- Better timeout handling

### 2. Form JavaScript (`form.blade.php`)

**Improved upload process:**
- Better error messages from Cloudflare
- Proper timeout handling (2 hours)
- Extracts video ID from upload response
- Better progress tracking

---

## 🧪 Testing

### Test 1: Video Name

1. **Upload a video** with a specific title
2. **Check Cloudflare dashboard** - Video should have the correct name
3. **Verify in database** - Title should match

### Test 2: Large Video Upload

1. **Upload a video > 500MB**
2. **Should not get 400 error**
3. **Progress bar should show real progress**
4. **Video should upload successfully**

---

## 🔍 Troubleshooting

### Still Getting 400 Error?

**Check Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Cloudflare Direct Upload Response` - Shows what was sent
- Error details from Cloudflare

**Common causes:**
1. **File too large** - Cloudflare has limits (check your plan)
2. **Invalid API token** - Verify token has Stream:Edit permission
3. **Account limits** - Check Cloudflare account limits
4. **Network issues** - Check browser console for network errors

### Video Name Still Not Appearing?

**Verify:**
1. Title is being sent in the request (check browser Network tab)
2. Cloudflare API is receiving metadata (check logs)
3. Video is created with metadata (check Cloudflare dashboard)

**Manual check:**
```javascript
// In browser console, check the request:
// Should see: meta: { name: "Your Video Title" }
```

### Upload Timeout?

**Solutions:**
1. **Increase timeout** - Already set to 2 hours, can increase if needed
2. **Check file size** - Very large files (>10GB) may need chunked upload
3. **Check network** - Stable connection needed for large uploads

---

## 📊 Current Configuration

### File Size Thresholds

| File Size | Method | Timeout | allowedOrigins |
|-----------|--------|--------|----------------|
| < 5MB | Server upload | N/A | N/A |
| 5MB - 500MB | Direct upload | 2 hours | Yes |
| > 500MB | Direct upload | 4 hours | No (prevents 400) |

### Video Metadata

- **Name**: Sent via `meta.name` in direct upload request
- **Duration**: Set via `maxDurationSeconds`
- **Signed URLs**: Disabled (`requireSignedURLs: false`)

---

## 🎯 Best Practices

### 1. File Size Recommendations

- **Small videos** (<100MB): Use server-side upload (faster)
- **Medium videos** (100-500MB): Direct upload works well
- **Large videos** (>500MB): Direct upload (automatic, no allowedOrigins)

### 2. Video Naming

- Use descriptive titles
- Avoid special characters that might cause issues
- Keep under 255 characters

### 3. Error Handling

- Check browser console for JavaScript errors
- Check Laravel logs for server-side errors
- Check Cloudflare dashboard for upload status

---

## 📝 Code Changes Summary

### Controller Changes

1. ✅ Added `meta.name` to payload for video title
2. ✅ Removed `allowedOrigins` for large files (>500MB)
3. ✅ Increased timeout for large files (4 hours)
4. ✅ Better error logging

### JavaScript Changes

1. ✅ Better error message extraction
2. ✅ Proper timeout handling (2 hours)
3. ✅ Video ID extraction from upload response
4. ✅ Improved progress tracking

---

## 🔧 Manual Testing

### Test Video Name:

```bash
# Check if video name is in Cloudflare
curl -X GET "https://api.cloudflare.com/client/v4/accounts/YOUR_ACCOUNT_ID/stream/VIDEO_ID" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

Should see:
```json
{
  "result": {
    "uid": "...",
    "meta": {
      "name": "Your Video Title"
    }
  }
}
```

### Test Large Upload:

1. Select file > 500MB
2. Check browser Network tab
3. Verify upload request doesn't include `allowedOrigins`
4. Monitor progress bar
5. Check for 400 errors

---

## ✅ Verification Checklist

After fixes:

- [ ] Video name appears in Cloudflare dashboard
- [ ] Large videos (>500MB) upload without 400 error
- [ ] Progress bar shows real progress
- [ ] No errors in browser console
- [ ] No errors in Laravel logs
- [ ] Video appears in database with correct status

---

## 🆘 Still Having Issues?

### Check These:

1. **API Token**: Has "Stream:Edit" permission?
2. **Account ID**: Correct in `.env`?
3. **File Size**: Within Cloudflare limits?
4. **Network**: Stable connection?
5. **Browser**: Try different browser?
6. **Logs**: Check both Laravel and browser console

### Get Help:

1. Check `storage/logs/laravel.log` for detailed errors
2. Check browser console (F12) for JavaScript errors
3. Check Cloudflare dashboard for upload status
4. Review Cloudflare API documentation

---

**Last Updated**: January 1, 2026  
**Version**: 1.2 (Video Name & Large File Fixes)

