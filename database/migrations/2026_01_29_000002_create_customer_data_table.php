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
        Schema::create('customer_data', function (Blueprint $table) {
            $table->id();
            $table->string('ulp_code')->nullable(); // Kode ULP (23200, 23201, dll)
            $table->string('ulp_name'); // Nama ULP (ULP BPN SELATAN, dll)
            $table->string('month'); // Bulan (JAN, FEB, MAR, dll)
            $table->integer('year'); // Tahun (2025, 2026)
            $table->string('data_type')->default('bulanan'); // bulanan atau kumulatif
            $table->integer('customer_count')->default(0); // Jumlah pelanggan
            $table->timestamps();
            
            // Index untuk performance
            $table->index('ulp_code');
            $table->index('ulp_name');
            $table->index(['month', 'year']);
            $table->index('data_type');
            $table->unique(['ulp_code', 'month', 'year', 'data_type'], 'unique_customer_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_data');
    }
};
