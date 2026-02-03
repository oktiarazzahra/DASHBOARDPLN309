<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_data', function (Blueprint $table) {
            $table->id();
            $table->string('ulp_code', 10);
            $table->string('ulp_name');
            $table->string('month', 3); // JAN, FEB, etc
            $table->integer('year');
            $table->string('data_type', 20); // bulanan, kumulatif
            $table->bigInteger('kwh_jual')->default(0); // kWh yang dijual
            $table->bigInteger('rp_pendapatan')->default(0); // Rupiah pendapatan
            $table->decimal('rp_per_kwh', 10, 2)->default(0); // Rp/kWh
            $table->timestamps();
            
            $table->index(['ulp_code', 'year', 'month']);
            $table->index(['year', 'data_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_data');
    }
};
