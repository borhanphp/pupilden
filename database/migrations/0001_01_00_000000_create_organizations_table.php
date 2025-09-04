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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('custom_domain')->nullable();

            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Social media
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('pinterest')->nullable();

            // Branding & Status
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);

            // Communication settings
            $table->boolean('sms_active')->default(true);
            $table->boolean('email_active')->default(true);
            $table->boolean('whatsapp_active')->default(true);

            $table->unsignedInteger('sms_limit')->default(100);
            $table->unsignedInteger('email_limit')->default(100);
            $table->unsignedInteger('whatsapp_limit')->default(100);

            $table->string('plan_type')->default('free');
            $table->string('status')->default('active');

            // Auditing
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
