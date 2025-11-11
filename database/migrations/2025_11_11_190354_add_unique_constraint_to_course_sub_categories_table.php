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
        // Drop the old unique constraint on slug column
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
        
        // Add composite unique constraint on organization_id and slug
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->unique(['organization_id', 'slug'], 'course_sub_categories_organization_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the composite unique constraint
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->dropUnique('course_sub_categories_organization_slug_unique');
        });
        
        // Restore the old unique constraint on slug column
        Schema::table('course_sub_categories', function (Blueprint $table) {
            $table->unique('slug');
        });
    }
};
