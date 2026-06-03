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
        Schema::table('exams', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in minutes');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->text('feedback')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['description', 'duration']);
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }
};
