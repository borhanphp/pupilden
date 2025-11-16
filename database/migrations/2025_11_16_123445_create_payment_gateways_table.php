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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->string('gateway_name'); // stripe, paypal, razorpay, etc.
            $table->string('display_name'); // Display name for the gateway
            $table->boolean('is_active')->default(true);
            $table->boolean('is_manual')->default(false); // Manual mode for testing
            $table->boolean('is_default')->default(false); // Only one default per organization
            $table->json('credentials')->nullable(); // Store API keys, merchant IDs, etc. as JSON
            $table->json('settings')->nullable(); // Additional settings like currency, mode (sandbox/live), etc.
            $table->text('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
