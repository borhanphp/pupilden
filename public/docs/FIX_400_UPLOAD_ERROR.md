# Fix: 400 Error on Cloudflare Upload

## 🐛 Problem

Getting **400 Bad Request** error when uploading file to Cloudflare Stream direct upload URL.

---

## ✅ What I Fixed

### 1. **Added FormData Support**
Cloudflare Stream's direct upload endpoint expects the file to be sent as `multipart/form-data` with the file in a `file` field.

**Before:**
```javascript
xhr.send(file); // Raw file - causes 400 error
```

**After:**
```javascript
const formData = new FormData();
formData.append('file', file);
xhr.send(formData); // FormData - correct format
```

### 2. **Added Fallback Method**
The code now tries both methods:
1. **First**: Raw file upload (for compatibility)
2. **If 400 error**: Automatically retries with FormData

### 3. **Better Error Logging**
- Logs full error response from Cloudflare
- Shows detailed error messages
- Console logging for debugging

---

## 🔍 How to Debug

### Step 1: Check Browser Console

Open browser console (F12) and look for:

```
Upload response status: 400
Upload response: {...}
Cloudflare upload error: {...}
```

This will show you the exact error from Cloudflare.

### Step 2: Check Network Tab

1. Open browser DevTools (F12)
2. Go to **Network** tab
3. Try uploading a video
4. Find the request to `upload.cloudflarestream.com`
5. Check:
   - **Request Headers**: Should have `Content-Type: multipart/form-data`
   - **Request Payload**: Should show FormData with file
   - **Response**: Shows Cloudflare's error message

### Step 3: Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Cloudflare Direct Upload Response` - Shows upload URL creation
- Any error messages

---

## 🎯 Common 400 Error Causes

### 1. Wrong Content-Type

**Error**: "Invalid content type"

**Fix**: ✅ Already fixed - Now uses FormData which sets correct Content-Type

### 2. Missing File Field

**Error**: "File field required"

**Fix**: ✅ Already fixed - File is now in `file` field in FormData

### 3. File Too Large

**Error**: "File size exceeds limit"

**Solution**: 
- Check Cloudflare account limits
- Some plans have file size restrictions
- Contact Cloudflare support if needed

### 4. Invalid File Format

**Error**: "Unsupported file format"

**Solution**: 
- Ensure file is valid video format (MP4, AVI, MOV, etc.)
- Check file is not corrupted
- Try re-encoding the video

### 5. CORS Issues

**Error**: "CORS policy" or "Origin not allowed"

**Solution**: 
- Check `allowedOrigins` in upload URL request
- Verify your domain is correct in `.env` (`APP_URL`)
- For localhost, this might not work - use production domain

---

## 🧪 Testing

### Test 1: Small File (< 100MB)

1. Upload a small video file
2. Should work with FormData method
3. Check browser console for success

### Test 2: Large File (> 500MB)

1. Upload a large video file
2. Should automatically use FormData
3. Progress bar should show real progress
4. Should complete without 400 error

### Test 3: Check Network Tab

1. Open DevTools → Network
2. Upload a video
3. Find `upload.cloudflarestream.com` request
4. Check:
   - ✅ Status: 200 (success) or 201
   - ✅ Request has FormData
   - ✅ Response has video ID

---

## 🔧 Manual Testing

### Test Upload URL Directly

You can test the upload URL manually:

```bash
curl -X POST "YOUR_UPLOAD_URL" \
  -F "file=@/path/to/video.mp4"
```

**Expected Response:**
```json
{
  "result": {
    "uid": "video-id-here"
  },
  "success": true
}
```

---

## 📊 Current Upload Flow

```
1. Get upload URL from Laravel
   ↓
2. Create FormData with file
   ↓
3. POST to Cloudflare upload URL
   ↓
4. Cloudflare processes file
   ↓
5. Get video ID from response
   ↓
6. Submit form with video ID
```

---

## 🐛 Troubleshooting Steps

### If Still Getting 400:

1. **Check Browser Console**
   - Look for detailed error message
   - Check Network tab for request/response

2. **Verify File Format**
   - Is it a valid video file?
   - Try a different video file
   - Check file is not corrupted

3. **Check File Size**
   - Is it within Cloudflare limits?
   - Try a smaller file first

4. **Check Network**
   - Stable internet connection?
   - No firewall blocking?
   - Try different network

5. **Check Cloudflare Account**
   - Is Stream enabled?
   - Check account limits
   - Verify API token permissions

---

## 💡 Code Changes Summary

### JavaScript Changes:

1. ✅ **Added FormData support** - File now sent as multipart/form-data
2. ✅ **Added fallback** - Tries raw file first, then FormData if 400 error
3. ✅ **Better error logging** - Shows detailed Cloudflare errors
4. ✅ **Console logging** - Easier debugging

### How It Works:

```javascript
// Try raw file first
try {
    uploadResult = await uploadFile(false);
} catch (error) {
    // If 400 error, try FormData
    if (error.message.includes('400')) {
        uploadResult = await uploadFile(true);
    }
}
```

---

## ✅ Verification

After fix, you should see:

1. **Browser Console**: 
   - "Upload response status: 200" or "201"
   - "Parsed upload result: {uid: '...'}"

2. **Network Tab**:
   - Request to `upload.cloudflarestream.com`
   - Status: 200/201
   - Response with video ID

3. **Video Upload**:
   - Progress bar completes
   - Form submits successfully
   - Video appears in database

---

## 🆘 Still Having Issues?

### Get Detailed Error:

1. **Open Browser Console** (F12)
2. **Try uploading**
3. **Look for error messages**
4. **Check Network tab** for full request/response

### Common Solutions:

- **400 with "Invalid file"**: Check file format and size
- **400 with "CORS"**: Check `APP_URL` in `.env`
- **400 with no message**: Check browser console for details
- **Network error**: Check internet connection

---

## 📝 Next Steps

1. **Test upload** - Try uploading a video
2. **Check console** - Look for any errors
3. **Verify success** - Video should appear in database
4. **Check Cloudflare** - Video should appear in Stream dashboard

---

**Last Updated**: January 2, 2026  
**Version**: 1.3 (FormData Fix for 400 Error)

