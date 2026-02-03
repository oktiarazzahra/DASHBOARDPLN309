<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_customer_data', function (Blueprint $table) {
            $table->id();
            $table->string('tarif_code'); // S1/220VA, S2/450VA, R1/900VA, etc
            $table->string('tarif_name'); // Nama lengkap tarif
            $table->string('tarif_category'); // S, R, B, I, P, T, C, L
            $table->integer('year'); // 2025
            $table->integer('month'); // 0-11 (JAN=0, DEC=11)
            $table->string('month_name'); // JAN, FEB, MAR, ...
            $table->bigInteger('total_customers'); // Jumlah pelanggan
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['year', 'month']);
            $table->index('tarif_category');
            $table->unique(['tarif_code', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_customer_data');
    }
};
