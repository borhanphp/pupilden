# Video Upload Optimization Guide

This guide explains how to optimize video uploads in your Laravel application using queues, improved configurations, and user feedback.

## Table of Contents
1. [Overview](#overview)
2. [PHP Configuration](#php-configuration)
3. [Laravel Queue Setup](#laravel-queue-setup)
4. [Environment Configuration](#environment-configuration)
5. [Database Migration](#database-migration)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## Overview

The video upload system has been optimized with the following improvements:

1. **Queue-based Processing**: Large video uploads are processed in the background using Laravel queues
2. **Upload Progress Indicator**: Users see a visual progress bar and animation during upload
3. **Increased Upload Limits**: PHP and server configurations support larger files
4. **Status Tracking**: Videos have upload status tracking (pending, processing, completed, failed)

---

## PHP Configuration

### 1. Update php.ini

You need to modify your PHP configuration to allow large file uploads. The php.ini file is typically located at:
- **XAMPP**: `C:\xampp\php\php.ini` or `E:\xampp8.2.12\php\php.ini`
- **Linux**: `/etc/php/8.2/apache2/php.ini` or `/etc/php/8.2/fpm/php.ini`

Update the following settings:

```ini
; Maximum size of POST data that PHP will accept
post_max_size = 2048M

; Maximum allowed size for uploaded files
upload_max_filesize = 2048M

; Maximum execution time of each script (in seconds)
max_execution_time = 3600

; Maximum amount of time each script may spend parsing request data
max_input_time = 3600

; Maximum amount of memory a script may consume
memory_limit = 512M
```

### 2. Restart Apache/Web Server

After modifying php.ini, restart your web server:

**XAMPP (Windows)**:
- Open XAMPP Control Panel
- Stop Apache
- Start Apache

**Linux**:
```bash
sudo service apache2 restart
# OR
sudo systemctl restart php8.2-fpm
```

### 3. Verify Changes

Create a test file `phpinfo.php` in your project root:

```php
<?php
phpinfo();
?>
```

Visit `http://localhost/pupilden/phpinfo.php` and search for:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`

**Important**: Delete this file after verification for security reasons.

---

## Laravel Queue Setup

### 1. Choose Queue Driver

Update your `.env` file with one of the following queue drivers:

#### Option A: Database Queue (Recommended for Development)

```env
QUEUE_CONNECTION=database
```

Create the jobs table:
```bash
php artisan queue:table
php artisan migrate
```

#### Option B: Redis Queue (Recommended for Production)

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Install Redis:
- **Windows**: Download from https://github.com/microsoftarchive/redis/releases
- **Linux**: `sudo apt-get install redis-server`

#### Option C: Sync (For Testing - No Background Processing)

```env
QUEUE_CONNECTION=sync
```

### 2. Run Queue Worker

Start the queue worker to process background jobs:

**Development**:
```bash
php artisan queue:work
```

**Production** (with supervisor or systemd):
```bash
php artisan queue:work --daemon --tries=3 --timeout=3600
```

**Windows (Keep terminal open)**:
```bash
php artisan queue:work --tries=3 --timeout=3600
```

### 3. Auto-restart Queue Worker (Optional)

For production, use Supervisor (Linux) or Task Scheduler (Windows) to keep the queue worker running.

**Supervisor Configuration** (`/etc/supervisor/conf.d/laravel-worker.conf`):
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/pupilden/artisan queue:work --sleep=3 --tries=3 --timeout=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/pupilden/storage/logs/worker.log
```

---

## Environment Configuration

### Update .env File

Add or update the following configurations in your `.env` file:

```env
# Queue Configuration
QUEUE_CONNECTION=database

# Cloudflare Stream Configuration
CLOUDFLARE_API_TOKEN=your_api_token_here
CLOUDFLARE_ACCOUNT_ID=your_account_id_here

# Session Configuration (for large uploads)
SESSION_LIFETIME=120

# File Upload Settings
FILESYSTEM_DISK=local
```

### Cloudflare Stream Setup

1. Log in to your Cloudflare account
2. Go to Stream section
3. Get your Account ID from the dashboard
4. Create an API Token:
   - Go to "My Profile" → "API Tokens"
   - Create Token with "Stream:Edit" permissions
   - Copy the token to your `.env` file

---

## Database Migration

Run the migration to add upload status tracking fields:

```bash
php artisan migrate
```

This adds the following columns to the `videos` table:
- `upload_status`: pending, processing, completed, failed
- `upload_progress`: 0-100 (percentage)
- `upload_error`: Error message if upload fails

---

## Testing

### 1. Test Small Video Upload (YouTube/S3)

1. Go to Videos → Add New Video
2. Select "YouTube" or "S3" as video type
3. Enter a video URL
4. Submit the form
5. Video should be created immediately

### 2. Test Large Video Upload (Cloudflare)

1. Go to Videos → Add New Video
2. Select "Cloudflare" as video type
3. Choose a large video file (e.g., 100MB+)
4. Submit the form
5. You should see:
   - Upload progress overlay with animation
   - Progress bar showing upload status
   - Success message: "Video is being uploaded in the background"

### 3. Monitor Queue Processing

Open a terminal and run:
```bash
php artisan queue:work --verbose
```

You should see:
```
[2025-12-29 09:15:30] Processing: App\Jobs\ProcessVideoUpload
[2025-12-29 09:16:45] Processed:  App\Jobs\ProcessVideoUpload
```

### 4. Check Video Status

1. Go to Videos list
2. The video should show upload status
3. Once completed, the video will be playable

---

## Troubleshooting

### Issue 1: Upload Fails with "File Too Large"

**Solution**:
- Check php.ini settings (see PHP Configuration section)
- Restart Apache/web server
- Clear browser cache
- Verify with phpinfo()

### Issue 2: Queue Jobs Not Processing

**Solution**:
- Check if queue worker is running: `php artisan queue:work`
- Verify QUEUE_CONNECTION in .env
- Check database for failed_jobs: `SELECT * FROM failed_jobs;`
- Retry failed jobs: `php artisan queue:retry all`

### Issue 3: Upload Progress Stuck at 90%

**Solution**:
- This is normal - progress bar shows simulated progress
- Actual upload happens in background
- Check queue worker logs for real progress
- Video status will update when complete

### Issue 4: Cloudflare Upload Fails

**Solution**:
- Verify CLOUDFLARE_API_TOKEN and CLOUDFLARE_ACCOUNT_ID in .env
- Check API token permissions (needs Stream:Edit)
- Check Cloudflare account has Stream enabled
- Review logs: `storage/logs/laravel.log`

### Issue 5: Memory Limit Exceeded

**Solution**:
- Increase memory_limit in php.ini (e.g., 512M or 1024M)
- Restart web server
- For very large files, consider chunked uploads

### Issue 6: Timeout During Upload

**Solution**:
- Increase max_execution_time in php.ini
- Increase max_input_time in php.ini
- Ensure queue worker has sufficient timeout: `--timeout=3600`

---

## Performance Tips

### 1. Speed Up Uploads

- **Use CDN**: Cloudflare Stream provides global CDN
- **Compress Videos**: Pre-compress videos before upload
- **Optimize Network**: Use wired connection for large uploads
- **Chunk Uploads**: Consider implementing chunked uploads for files > 1GB

### 2. Queue Optimization

- **Multiple Workers**: Run multiple queue workers for parallel processing
  ```bash
  php artisan queue:work --queue=default --tries=3 &
  php artisan queue:work --queue=default --tries=3 &
  ```
- **Priority Queues**: Separate high-priority uploads
  ```php
  ProcessVideoUpload::dispatch($video, ...)->onQueue('high-priority');
  ```

### 3. Database Optimization

- **Index**: Add index on upload_status for faster queries
  ```php
  $table->index('upload_status');
  ```
- **Clean Old Jobs**: Regularly clean completed jobs
  ```bash
  php artisan queue:flush
  ```

---

## Production Checklist

Before deploying to production:

- [ ] Update php.ini with production values
- [ ] Set QUEUE_CONNECTION=redis (not sync or database)
- [ ] Configure Supervisor to keep queue workers running
- [ ] Set up monitoring for failed jobs
- [ ] Configure log rotation for worker logs
- [ ] Test with actual production file sizes
- [ ] Set up Cloudflare Stream webhooks for status updates
- [ ] Configure email notifications for failed uploads
- [ ] Set up backup queue worker
- [ ] Document queue worker restart procedures

---

## Additional Resources

- [Laravel Queues Documentation](https://laravel.com/docs/10.x/queues)
- [Cloudflare Stream API](https://developers.cloudflare.com/stream/)
- [PHP Configuration](https://www.php.net/manual/en/ini.core.php)
- [Supervisor Documentation](http://supervisord.org/)

---

## Support

If you encounter issues not covered in this guide:

1. Check `storage/logs/laravel.log` for error messages
2. Review queue worker output for job failures
3. Verify all configuration settings
4. Test with smaller files first
5. Contact your system administrator for server-level issues

---

**Last Updated**: December 29, 2025
**Version**: 1.0

