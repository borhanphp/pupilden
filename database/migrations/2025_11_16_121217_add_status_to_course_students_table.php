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
        Schema::table('course_students', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('student_id');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->timestamp('disapproved_at')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_students', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_reason', 'approved_at', 'disapproved_at']);
        });
    }
};
