<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule queue processing for shared hosting
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=2')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Clean up old failed jobs daily
Schedule::command('queue:flush')
    ->daily();

// Clean up temporary video files older than 1 day
Schedule::call(function () {
    $files = Storage::files('temp_videos');
    foreach ($files as $file) {
        if (Storage::exists($file) && Storage::lastModified($file) < now()->subDay()->timestamp) {
            Storage::delete($file);
        }
    }
})->daily();
