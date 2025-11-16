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
        Schema::create('organization_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade')->unique();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('template')->nullable();
            $table->string('primary_color')->nullable();
            $table->text('privacy_policy_content')->nullable();
            $table->text('about_us_content')->nullable();
            $table->string('footer_color')->nullable();
            $table->string('footer_design')->nullable();
            $table->text('copyright_text')->nullable();
            $table->string('business_email')->nullable();
            $table->string('banner')->nullable();
            $table->text('hero_text')->nullable();
            $table->string('baksh_number')->nullable();
            $table->string('ngad_number')->nullable();
            $table->string('rocket_number')->nullable();
            $table->string('celfin_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_settings');
    }
};
