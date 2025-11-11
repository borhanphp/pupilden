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
        // Drop the old unique constraint on code column
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
        
        // Add composite unique constraint on organization_id and code
        Schema::table('coupons', function (Blueprint $table) {
            $table->unique(['organization_id', 'code'], 'coupons_organization_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the composite unique constraint
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropUnique('coupons_organization_code_unique');
        });
        
        // Restore the old unique constraint on code column
        Schema::table('coupons', function (Blueprint $table) {
            $table->unique('code');
        });
    }
};
