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
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('layout_id')->nullable()->constrained('section_layouts')->nullOnDelete();
            $table->string('section_type'); // e.g. hero, about, features
            $table->string('title')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('layout_config')->nullable(); // Tailwind grid/flex structure
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
