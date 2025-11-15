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
        Schema::create('section_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "3-column grid"
            $table->string('slug')->unique(); // "grid-3-col"
            $table->json('layout_config'); // Tailwind classes, breakpoints, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_layouts');
    }
};
