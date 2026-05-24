<?php

namespace App\Providers;

use App\Services\GmailService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GmailService::class, function () {
            return new GmailService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Force HTTPS in production so all route() and asset() calls produce https:// URLs.
        // This prevents Mixed Content errors when running behind a reverse proxy (nginx/Cloudflare).
        if ($this->app->environment('production') || str_starts_with(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }
    }
}
