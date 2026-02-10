<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add ulp_code column to tarif_customer_data
        Schema::table('tarif_customer_data', function (Blueprint $table) {
            $table->string('ulp_code')->nullable()->after('id');
            $table->string('ulp_name')->nullable()->after('ulp_code');
            
            // Drop unique constraint yang lama
            $table->dropUnique(['tarif_code', 'year', 'month']);
            
            // Tambah unique constraint baru dengan ulp_code
            $table->unique(['tarif_code', 'ulp_code', 'year', 'month']);
            
            // Add index for ulp_code
            $table->index('ulp_code');
        });

        // Add ulp_code column to tarif_power_data
        Schema::table('tarif_power_data', function (Blueprint $table) {
            $table->string('ulp_code')->nullable()->after('id');
            $table->string('ulp_name')->nullable()->after('ulp_code');
            
            // Drop unique constraint yang lama
            $table->dropUnique(['tarif_code', 'year', 'month']);
            
            // Tambah unique constraint baru dengan ulp_code
            $table->unique(['tarif_code', 'ulp_code', 'year', 'month']);
            
            // Add index for ulp_code
            $table->index('ulp_code');
        });

        // Add ulp_code column to tarif_revenue_data
        Schema::table('tarif_revenue_data', function (Blueprint $table) {
            $table->string('ulp_code')->nullable()->after('id');
            $table->string('ulp_name')->nullable()->after('ulp_code');
            
            // Drop unique constraint yang lama
            $table->dropUnique(['tarif_code', 'year', 'month', 'data_type']);
            
            // Tambah unique constraint baru dengan ulp_code
            $table->unique(['tarif_code', 'ulp_code', 'year', 'month', 'data_type']);
            
            // Add index for ulp_code
            $table->index('ulp_code');
        });
    }

    public function down(): void
    {
        Schema::table('tarif_customer_data', function (Blueprint $table) {
            $table->dropUnique(['tarif_code', 'ulp_code', 'year', 'month']);
            $table->dropIndex(['ulp_code']);
            $table->dropColumn(['ulp_code', 'ulp_name']);
            $table->unique(['tarif_code', 'year', 'month']);
        });

        Schema::table('tarif_power_data', function (Blueprint $table) {
            $table->dropUnique(['tarif_code', 'ulp_code', 'year', 'month']);
            $table->dropIndex(['ulp_code']);
            $table->dropColumn(['ulp_code', 'ulp_name']);
            $table->unique(['tarif_code', 'year', 'month']);
        });

        Schema::table('tarif_revenue_data', function (Blueprint $table) {
            $table->dropUnique(['tarif_code', 'ulp_code', 'year', 'month', 'data_type']);
            $table->dropIndex(['ulp_code']);
            $table->dropColumn(['ulp_code', 'ulp_name']);
            $table->unique(['tarif_code', 'year', 'month', 'data_type']);
        });
    }
};
