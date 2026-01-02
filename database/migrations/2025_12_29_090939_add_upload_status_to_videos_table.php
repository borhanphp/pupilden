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
            // PostgreSQL doesn't support 'after()', columns will be added at the end
            $table->string('upload_status')->default('pending'); // pending, processing, completed, failed
            $table->integer('upload_progress')->default(0); // 0-100
            $table->text('upload_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
           $table->dropColumn(['upload_status', 'upload_progress', 'upload_error']);
        });
    }
};
