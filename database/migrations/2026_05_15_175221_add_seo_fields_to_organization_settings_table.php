<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('currency_symbol');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords', 1000)->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('meta_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'og_image']);
        });
    }
};
