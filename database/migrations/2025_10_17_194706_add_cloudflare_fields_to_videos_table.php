<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('cloudflare_video_id')->nullable()->after('video_url');
            $table->string('thumbnail_url')->nullable()->after('cloudflare_video_id');
            $table->bigInteger('file_size')->nullable()->after('thumbnail_url');
            $table->boolean('is_published')->default(true)->after('is_preview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['cloudflare_video_id', 'thumbnail_url', 'file_size', 'is_published']);
        });
    }
};
