# Laravel Queue Setup for Shared Hosting

## 🌐 Shared Hosting Challenges

Shared hosting typically doesn't allow:
- Running continuous background processes (`php artisan queue:work`)
- SSH access with long-running processes
- Supervisor or systemd services

## ✅ Solutions for Shared Hosting

---

## Solution 1: Cron Job (RECOMMENDED)

This is the most reliable solution for shared hosting.

### Step 1: Update .env

```env
QUEUE_CONNECTION=database
```

### Step 2: Set Up Cron Job

Add this cron job in your hosting control panel (cPanel, Plesk, etc.):

**Run every minute:**
```bash
* * * * * cd /home/username/public_html/pupilden && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

**Important**: Replace `/home/username/public_html/pupilden` with your actual path.

### How It Works
- Cron runs every minute
- Processes all pending jobs
- Stops when queue is empty
- `--max-time=50` ensures it stops before next cron run (60 seconds)

### Finding Your Path

**cPanel**:
1. Go to File Manager
2. Check the address bar, path is usually: `/home/username/public_html/`

**Via SSH** (if available):
```bash
pwd
```

---

## Solution 2: Laravel Scheduler + Cron (BETTER)

This is the recommended approach for shared hosting.

### Step 1: Update app/Console/Kernel.php

Add this to the `schedule()` method:

```php
protected function schedule(Schedule $schedule)
{
    // Process queue every minute
    $schedule->command('queue:work --stop-when-empty --max-time=50')
             ->everyMinute()
             ->withoutOverlapping();
}
```

### Step 2: Set Up Single Cron Job

Add only ONE cron job:

```bash
* * * * * cd /home/username/public_html/pupilden && php artisan schedule:run >> /dev/null 2>&1
```

### Benefits
- Only one cron job needed
- Easier to manage
- Laravel handles the scheduling
- Prevents overlapping jobs

---

## Solution 3: Process on Web Request (SIMPLE)

For low-traffic sites with occasional uploads.

### Step 1: Update .env

```env
QUEUE_CONNECTION=sync
```

### How It Works
- Jobs process immediately when created
- No cron job needed
- User waits for upload to complete
- Still shows progress animation

### Pros & Cons
- ✅ No cron setup needed
- ✅ Works on any hosting
- ❌ User must wait for upload
- ❌ May timeout on very large files

---

## Solution 4: Hybrid Approach (BALANCED)

Combine sync for small files, queue for large files.

### Create app/Services/VideoUploadService.php

```php
<?php

namespace App\Services;

use App\Jobs\ProcessVideoUpload;
use App\Models\Video;

class VideoUploadService
{
    public function handleUpload($file, Video $video)
    {
        $fileSize = $file->getSize();
        $maxSyncSize = 50 * 1024 * 1024; // 50MB
        
        if ($fileSize > $maxSyncSize) {
            // Large file - use queue
            $tempPath = $file->storeAs('temp_videos', time() . '_' . $file->getClientOriginalName());
            ProcessVideoUpload::dispatch($video, $tempPath, $file->getClientOriginalName(), $fileSize);
            return ['queued' => true];
        } else {
            // Small file - process immediately
            // Upload directly to Cloudflare
            return $this->uploadDirectly($file, $video);
        }
    }
    
    private function uploadDirectly($file, Video $video)
    {
        // Your direct upload logic here
    }
}
```

---

## 🛠️ Setting Up Cron Job on Different Hosting Panels

### cPanel

1. Log in to cPanel
2. Find "Cron Jobs" under Advanced
3. Select "Once Per Minute" from Common Settings
4. Enter command:
   ```
   cd /home/username/public_html/pupilden && php artisan queue:work --stop-when-empty --max-time=50
   ```
5. Click "Add New Cron Job"

**Screenshot locations:**
- Advanced → Cron Jobs
- Or search for "cron" in cPanel search

### Plesk

1. Log in to Plesk
2. Go to Websites & Domains
3. Click "Scheduled Tasks"
4. Click "Add Task"
5. Set:
   - Task type: Run a command
   - Command: `/usr/bin/php /var/www/vhosts/yourdomain.com/httpdocs/pupilden/artisan queue:work --stop-when-empty --max-time=50`
   - Schedule: Every minute (*/1 * * * *)
6. Click "OK"

### DirectAdmin

1. Log in to DirectAdmin
2. Go to "Advanced Features"
3. Click "Cron Jobs"
4. Add new cron job with:
   - Minute: *
   - Hour: *
   - Day: *
   - Month: *
   - Weekday: *
   - Command: `cd /home/username/domains/yourdomain.com/public_html/pupilden && php artisan queue:work --stop-when-empty --max-time=50`
5. Click "Add"

---

## 📝 Complete Setup for Shared Hosting

### Step-by-Step Guide

#### 1. Update .env File

```env
QUEUE_CONNECTION=database
APP_URL=https://yourdomain.com
```

#### 2. Run Migration (via SSH or hosting file manager)

**Via SSH:**
```bash
php artisan migrate
```

**Via Browser** (create migrate.php in public folder):
```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate');
echo "Migration completed!";
// DELETE THIS FILE AFTER USE!
```

Visit: `https://yourdomain.com/migrate.php`
**IMPORTANT**: Delete this file after use!

#### 3. Update app/Console/Kernel.php

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:work --stop-when-empty --max-time=50')
             ->everyMinute()
             ->withoutOverlapping();
}
```

#### 4. Set Up Cron Job

In your hosting control panel, add:

```bash
* * * * * cd /home/username/public_html/pupilden && php artisan schedule:run >> /dev/null 2>&1
```

#### 5. Test the Setup

**Create test.php in public folder:**
```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Test queue
$kernel->call('queue:work', [
    '--stop-when-empty' => true,
    '--max-time' => 50
]);

echo "Queue processed!";
// DELETE THIS FILE AFTER USE!
```

Visit: `https://yourdomain.com/test.php`
**IMPORTANT**: Delete this file after use!

---

## 🔍 Verifying Cron Job is Working

### Method 1: Check Cron Job Logs

Most hosting panels show cron job execution logs.

**cPanel**: Cron Jobs → View Cron Job History
**Plesk**: Scheduled Tasks → View Logs

### Method 2: Create Log File

Update your cron command to log output:

```bash
* * * * * cd /home/username/public_html/pupilden && php artisan queue:work --stop-when-empty --max-time=50 >> /home/username/cron.log 2>&1
```

Then check the log file periodically.

### Method 3: Check Jobs Table

Run this SQL query:

```sql
SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
```

If jobs are being processed, the table should be empty or have very recent jobs.

### Method 4: Check Failed Jobs

```sql
SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 10;
```

---

## 🐛 Troubleshooting Shared Hosting

### Issue 1: Cron Job Not Running

**Symptoms:**
- Jobs stay in database
- Videos stuck on "pending" status

**Solutions:**
1. Check cron job syntax is correct
2. Verify path is absolute (not relative)
3. Check PHP path (might be `/usr/bin/php`, `/usr/local/bin/php`, or `php`)
4. Contact hosting support for correct PHP path

**Test PHP path:**
```bash
which php
```

### Issue 2: Permission Denied

**Symptoms:**
- Cron fails with permission error

**Solutions:**
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs
```

Or via File Manager:
1. Select `storage` and `bootstrap/cache` folders
2. Right-click → Change Permissions
3. Set to 755 (or 777 for storage/logs)

### Issue 3: Timeouts

**Symptoms:**
- Large videos fail to upload

**Solutions:**
1. Increase PHP limits in `.htaccess`:
   ```apache
   php_value upload_max_filesize 2048M
   php_value post_max_size 2048M
   php_value max_execution_time 3600
   php_value max_input_time 3600
   php_value memory_limit 512M
   ```

2. Or create `php.ini` in root:
   ```ini
   upload_max_filesize = 2048M
   post_max_size = 2048M
   max_execution_time = 3600
   max_input_time = 3600
   memory_limit = 512M
   ```

### Issue 4: Jobs Processing Too Slowly

**Solutions:**
1. Increase cron frequency (run every 30 seconds):
   ```bash
   * * * * * cd /path && php artisan queue:work --stop-when-empty --max-time=25
   * * * * * sleep 30 && cd /path && php artisan queue:work --stop-when-empty --max-time=25
   ```

2. Or process multiple jobs:
   ```bash
   * * * * * cd /path && php artisan queue:work --stop-when-empty --max-jobs=10 --max-time=50
   ```

---

## 💡 Best Practices for Shared Hosting

### 1. Use Database Queue
```env
QUEUE_CONNECTION=database
```
Don't use Redis unless your hosting supports it.

### 2. Set Reasonable Timeouts
```bash
--max-time=50
```
Always less than your cron frequency (60 seconds).

### 3. Limit Job Attempts
In `ProcessVideoUpload.php`:
```php
public $tries = 2; // Only retry once on shared hosting
```

### 4. Clean Up Old Jobs
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:work --stop-when-empty --max-time=50')
             ->everyMinute()
             ->withoutOverlapping();
    
    // Clean up old jobs daily
    $schedule->command('queue:flush')->daily();
    
    // Retry failed jobs once per hour
    $schedule->command('queue:retry all')->hourly();
}
```

### 5. Monitor Storage Space
Shared hosting has limited storage. Clean up temp files regularly:

```php
protected function schedule(Schedule $schedule)
{
    // ... other schedules ...
    
    // Clean temp videos older than 1 day
    $schedule->call(function () {
        $files = Storage::files('temp_videos');
        foreach ($files as $file) {
            if (Storage::lastModified($file) < now()->subDay()->timestamp) {
                Storage::delete($file);
            }
        }
    })->daily();
}
```

---

## 📊 Performance Optimization for Shared Hosting

### 1. Compress Videos Client-Side
Use JavaScript to compress before upload (for very large files).

### 2. Use Direct Upload to Cloudflare
Skip server storage entirely:
- Get upload URL from Cloudflare
- Upload directly from browser
- Notify Laravel when complete

### 3. Limit Concurrent Uploads
Add to your form validation:
```php
// Check if user has pending uploads
$pendingCount = Video::where('created_by', auth()->id())
    ->where('upload_status', 'pending')
    ->count();
    
if ($pendingCount >= 3) {
    return back()->withErrors(['video' => 'Please wait for current uploads to complete.']);
}
```

---

## ✅ Recommended Configuration for Shared Hosting

**app/Console/Kernel.php:**
```php
protected function schedule(Schedule $schedule)
{
    // Process queue
    $schedule->command('queue:work --stop-when-empty --max-time=50 --tries=2')
             ->everyMinute()
             ->withoutOverlapping();
    
    // Clean up
    $schedule->command('queue:flush')->daily();
    
    // Clean temp files
    $schedule->call(function () {
        $files = Storage::files('temp_videos');
        foreach ($files as $file) {
            if (Storage::lastModified($file) < now()->subDay()->timestamp) {
                Storage::delete($file);
            }
        }
    })->daily();
}
```

**Cron Job:**
```bash
* * * * * cd /home/username/public_html/pupilden && php artisan schedule:run >> /dev/null 2>&1
```

**.env:**
```env
QUEUE_CONNECTION=database
APP_ENV=production
APP_DEBUG=false
```

---

## 🎯 Summary for Shared Hosting

**Best Approach:**
1. Use database queue driver
2. Set up Laravel Scheduler
3. Add single cron job for scheduler
4. Monitor via hosting control panel

**Cron Command:**
```bash
* * * * * cd /home/username/public_html/pupilden && php artisan schedule:run >> /dev/null 2>&1
```

**This approach gives you:**
- ✅ Background video processing
- ✅ Automatic cleanup
- ✅ No long-running processes
- ✅ Works on all shared hosting
- ✅ Easy to maintain

---

## 📞 Getting Help from Your Hosting Provider

If you need assistance, ask your hosting provider:

1. **"What is the absolute path to my Laravel application?"**
   - Example answer: `/home/username/public_html/pupilden`

2. **"What is the full path to the PHP binary?"**
   - Example answer: `/usr/bin/php` or `/usr/local/bin/php7.4`

3. **"How do I set up a cron job to run every minute?"**
   - They'll guide you through their specific control panel

4. **"What are the PHP limits (upload_max_filesize, execution time)?"**
   - They'll tell you the limits or how to increase them

5. **"Do you support running Laravel scheduled tasks?"**
   - Most hosting providers do, and can help with setup

---

**Last Updated**: December 29, 2025  
**For**: Shared Hosting Environments

