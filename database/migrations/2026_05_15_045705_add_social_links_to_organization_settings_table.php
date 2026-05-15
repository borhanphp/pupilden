<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->string('site_name')->nullable()->after('business_email');
            $table->string('phone')->nullable()->after('site_name');
            $table->string('address')->nullable()->after('phone');
            $table->string('facebook_url')->nullable()->after('address');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
            $table->string('youtube_url')->nullable()->after('linkedin_url');
            $table->string('tiktok_url')->nullable()->after('youtube_url');
            $table->string('pinterest_url')->nullable()->after('tiktok_url');
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_name', 'phone', 'address',
                'facebook_url', 'twitter_url', 'instagram_url',
                'linkedin_url', 'youtube_url', 'tiktok_url', 'pinterest_url',
            ]);
        });
    }
};
