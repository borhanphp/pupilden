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
        Schema::create('section_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('page_sections')->onDelete('cascade');
            $table->integer('block_index')->default(0); // block order inside grid
            $table->string('key'); // e.g. title, image_url, description
            $table->longText('value')->nullable();
            $table->json('style')->nullable(); // Tailwind overrides per element
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_contents');
    }
};
