# Quick Start Guide - Video Upload Optimization

This is a simplified guide to get video upload optimization working quickly.

## 🚀 Quick Setup (5 Minutes)

### Step 1: Update PHP Configuration

1. Open `E:\xampp8.2.12\php\php.ini` in a text editor (as Administrator)
2. Find and update these lines:

```ini
post_max_size = 2048M
upload_max_filesize = 2048M
max_execution_time = 3600
max_input_time = 3600
memory_limit = 512M
```

3. Save the file
4. Restart Apache in XAMPP Control Panel (Stop → Start)

### Step 2: Update .env File

Add these lines to your `.env` file:

```env
QUEUE_CONNECTION=database
CLOUDFLARE_API_TOKEN=your_token_here
CLOUDFLARE_ACCOUNT_ID=your_account_id_here
```

### Step 3: Run Migrations

Open terminal in your project directory and run:

```bash
php artisan migrate
```

### Step 4: Start Queue Worker

Open a **NEW** terminal window and run:

```bash
php artisan queue:work
```

**Important**: Keep this terminal window open while uploading videos!

---

## ✅ Test It

1. Go to your application: `http://localhost/pupilden`
2. Navigate to Videos → Add New Video
3. Select "Cloudflare" as video type
4. Choose a video file
5. Click Submit
6. You should see an upload progress animation!

---

## 📋 What Changed?

### 1. **Queue-Based Processing**
- Videos now upload in the background
- No more browser timeouts
- Users can continue working while video uploads

### 2. **Upload Progress Animation**
- Visual feedback during upload
- Progress bar shows upload status
- Professional user experience

### 3. **Increased Upload Limits**
- Can now upload videos up to 2GB
- Longer execution time for large files
- Better memory management

---

## 🔍 How to Check if Queue is Working

### Check Queue Worker Status

In the terminal where you ran `php artisan queue:work`, you should see:

```
[2025-12-29 09:15:30] Processing: App\Jobs\ProcessVideoUpload
[2025-12-29 09:16:45] Processed:  App\Jobs\ProcessVideoUpload
```

### Check Video Status in Database

Run this SQL query:

```sql
SELECT id, title, upload_status, upload_progress FROM videos ORDER BY id DESC LIMIT 5;
```

You should see:
- `upload_status`: pending → processing → completed
- `upload_progress`: 0 → 100

---

## ❌ Common Issues & Solutions

### Issue: "File too large" error

**Solution**: 
- Check if you updated php.ini correctly
- Restart Apache
- Clear browser cache

### Issue: Queue worker not processing

**Solution**:
- Make sure queue worker is running: `php artisan queue:work`
- Check `.env` has `QUEUE_CONNECTION=database`
- Run `php artisan migrate` if not done yet

### Issue: Upload progress stuck

**Solution**:
- This is normal - check queue worker terminal for real progress
- Video will be ready when status changes to "completed"

### Issue: Cloudflare upload fails

**Solution**:
- Verify `CLOUDFLARE_API_TOKEN` and `CLOUDFLARE_ACCOUNT_ID` in `.env`
- Check Cloudflare account has Stream enabled
- Review logs: `storage/logs/laravel.log`

---

## 🎯 Next Steps

### For Development
- Keep queue worker running in a terminal
- Monitor logs: `tail -f storage/logs/laravel.log`

### For Production
- Use Redis instead of database queue
- Set up Supervisor to auto-restart queue workers
- Configure monitoring and alerts
- See `VIDEO_UPLOAD_OPTIMIZATION_GUIDE.md` for details

---

## 📚 Files Modified

1. **app/Jobs/ProcessVideoUpload.php** - Queue job for background processing
2. **app/Http/Controllers/VideoController.php** - Updated to use queue
3. **resources/views/videos/form.blade.php** - Added progress animation
4. **database/migrations/..._add_upload_status_to_videos_table.php** - Added status tracking

---

## 💡 Tips

### Speed Up Uploads
- Use wired internet connection
- Compress videos before upload
- Upload during off-peak hours

### Monitor Uploads
- Check queue worker terminal for progress
- Review `storage/logs/laravel.log` for errors
- Use `php artisan queue:failed` to see failed jobs

### Retry Failed Uploads
```bash
php artisan queue:retry all
```

---

## 🆘 Need Help?

1. Check `VIDEO_UPLOAD_OPTIMIZATION_GUIDE.md` for detailed documentation
2. Review `php-ini-video-upload-settings.txt` for PHP configuration
3. Check `env-video-upload-example.txt` for environment variables
4. Review Laravel logs: `storage/logs/laravel.log`

---

**Happy Uploading! 🎥**

