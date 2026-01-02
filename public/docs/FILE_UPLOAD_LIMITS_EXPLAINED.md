# File Upload Limits - Complete Explanation

## 🎯 What Was Fixed

The error **"The video file field must not be greater than 51200 kilobytes"** was caused by a mismatch between validation rules.

### Before (WRONG):
```php
'video_file' => 'nullable|file|max:51200', // Comment said "Max 50GB" but this is only 50MB!
```

### After (FIXED):
```php
'video_file' => 'nullable|file|max:2097152', // Max 2GB (2048MB = 2097152KB)
```

---

## 📊 Understanding File Upload Limits

There are **THREE** places where file upload limits must be configured:

### 1. PHP Configuration (php.ini)
```ini
upload_max_filesize = 2048M    # Maximum size for uploaded files
post_max_size = 2048M          # Maximum size of POST data (must be >= upload_max_filesize)
max_execution_time = 3600      # Maximum script execution time (1 hour)
max_input_time = 3600          # Maximum time to parse POST data
memory_limit = 512M            # Maximum memory per script
```

**Location:**
- XAMPP: `E:\xampp8.2.12\php\php.ini`
- Linux: `/etc/php/8.2/apache2/php.ini` or `/etc/php/8.2/fpm/php.ini`

**After editing, restart Apache!**

---

### 2. Laravel Validation (Controller)
```php
'video_file' => 'nullable|file|max:2097152', // Max 2GB in KILOBYTES
```

**Important:** Laravel's `max` validation is in **KILOBYTES**!

**Conversion:**
- 1 MB = 1,024 KB
- 1 GB = 1,048,576 KB
- 2 GB = 2,097,152 KB

**Common Sizes:**
- 50 MB = 51,200 KB
- 100 MB = 102,400 KB
- 500 MB = 512,000 KB
- 1 GB = 1,048,576 KB
- 2 GB = 2,097,152 KB
- 5 GB = 5,242,880 KB

---

### 3. Web Server Configuration (Optional)

**Apache (.htaccess in public folder):**
```apache
php_value upload_max_filesize 2048M
php_value post_max_size 2048M
php_value max_execution_time 3600
php_value max_input_time 3600
php_value memory_limit 512M
```

**Nginx:**
```nginx
client_max_body_size 2048M;
```

---

## 🔧 Current Configuration

Your application is now configured for:

| Setting | Value | Notes |
|---------|-------|-------|
| PHP upload_max_filesize | 2048M | Set in php.ini |
| PHP post_max_size | 2048M | Set in php.ini |
| Laravel validation | 2097152 KB | 2GB in KB |
| Max execution time | 3600 seconds | 1 hour |

---

## 📝 How to Change Upload Limits

### To Increase to 5GB:

**Step 1: Update php.ini**
```ini
upload_max_filesize = 5120M
post_max_size = 5120M
```

**Step 2: Update Laravel Validation**
```php
// 5GB = 5,242,880 KB
'video_file' => 'nullable|file|max:5242880',
```

**Step 3: Restart Apache**

---

### To Decrease to 500MB:

**Step 1: Update php.ini**
```ini
upload_max_filesize = 512M
post_max_size = 512M
```

**Step 2: Update Laravel Validation**
```php
// 500MB = 512,000 KB
'video_file' => 'nullable|file|max:512000',
```

**Step 3: Restart Apache**

---

## 🐛 Troubleshooting Upload Errors

### Error: "The video file field must not be greater than X kilobytes"

**Cause:** Laravel validation limit is too low

**Solution:** Increase the `max` value in validation rule (in KB)

---

### Error: "Maximum upload file size exceeded"

**Cause:** PHP upload_max_filesize is too low

**Solution:** 
1. Increase `upload_max_filesize` in php.ini
2. Also increase `post_max_size` (must be >= upload_max_filesize)
3. Restart Apache

---

### Error: "Maximum execution time exceeded"

**Cause:** Script timeout before upload completes

**Solution:**
1. Increase `max_execution_time` in php.ini
2. Increase `max_input_time` in php.ini
3. For shared hosting: This is why we use queues!

---

### Upload Starts But Never Completes

**Causes:**
- Browser timeout (solution: use queue processing)
- Network issues
- Server timeout

**Solution:** Use the queue system we implemented!

---

## 💡 Best Practices

### 1. Use Queue Processing for Large Files
For files > 100MB, always use queue processing:
- User doesn't wait for upload
- No browser timeouts
- Better user experience

### 2. Set Reasonable Limits
```
Small videos (tutorials): 500MB
Medium videos (courses): 1-2GB
Large videos (webinars): 2-5GB
```

### 3. Match All Configuration
Make sure PHP, Laravel, and Web Server limits match:
```
PHP upload_max_filesize = 2048M
PHP post_max_size = 2048M (same or larger)
Laravel max = 2097152 (KB equivalent)
```

### 4. Consider Storage Space
```
100 videos × 1GB each = 100GB storage needed
Plan your storage accordingly!
```

---

## 🎯 Quick Reference

### Current Setup (2GB Limit)

**php.ini:**
```ini
upload_max_filesize = 2048M
post_max_size = 2048M
max_execution_time = 3600
```

**VideoController.php:**
```php
'video_file' => 'nullable|file|max:2097152', // 2GB
```

**How to verify:**
1. Create `phpinfo.php` in public folder:
   ```php
   <?php phpinfo(); ?>
   ```
2. Visit: `http://localhost/pupilden/phpinfo.php`
3. Search for "upload_max_filesize" and "post_max_size"
4. **DELETE phpinfo.php after checking!**

---

## 📊 Size Conversion Table

| Megabytes (MB) | Kilobytes (KB) | Laravel max= |
|----------------|----------------|--------------|
| 50 MB | 51,200 KB | max:51200 |
| 100 MB | 102,400 KB | max:102400 |
| 250 MB | 256,000 KB | max:256000 |
| 500 MB | 512,000 KB | max:512000 |
| 1 GB (1024 MB) | 1,048,576 KB | max:1048576 |
| 2 GB (2048 MB) | 2,097,152 KB | max:2097152 |
| 5 GB (5120 MB) | 5,242,880 KB | max:5242880 |

**Formula:** MB × 1024 = KB

---

## ✅ Checklist After Changing Limits

- [ ] Updated php.ini
- [ ] Restarted Apache
- [ ] Updated Laravel validation in VideoController (both store and update methods)
- [ ] Tested with actual file upload
- [ ] Verified limits with phpinfo()
- [ ] Deleted phpinfo.php

---

## 🆘 Still Having Issues?

1. **Check PHP error log:**
   - XAMPP: `E:\xampp8.2.12\apache\logs\error.log`
   - Linux: `/var/log/apache2/error.log`

2. **Check Laravel log:**
   - `storage/logs/laravel.log`

3. **Verify PHP configuration:**
   ```bash
   php -i | grep upload_max_filesize
   php -i | grep post_max_size
   ```

4. **Test with smaller file first:**
   - Try 10MB file first
   - Then 100MB
   - Then your target size

---

**Last Updated:** December 29, 2025  
**Current Configuration:** 2GB limit with queue processing

