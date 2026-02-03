<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_power_data', function (Blueprint $table) {
            $table->id();
            $table->string('tarif_code');
            $table->string('tarif_name');
            $table->string('tarif_category');
            $table->integer('year');
            $table->integer('month');
            $table->string('month_name');
            $table->bigInteger('total_power'); // Dalam VA
            $table->timestamps();
            
            $table->index(['year', 'month']);
            $table->index('tarif_category');
            $table->unique(['tarif_code', 'year', 'month']);
        });

        Schema::create('tarif_revenue_data', function (Blueprint $table) {
            $table->id();
            $table->string('tarif_code');
            $table->string('tarif_name');
            $table->string('tarif_category');
            $table->integer('year');
            $table->integer('month');
            $table->string('month_name');
            $table->string('data_type'); // 'kwh' atau 'rp'
            $table->decimal('value', 20, 2); // kWh atau Rupiah
            $table->timestamps();
            
            $table->index(['year', 'month', 'data_type']);
            $table->index('tarif_category');
            $table->unique(['tarif_code', 'year', 'month', 'data_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_revenue_data');
        Schema::dropIfExists('tarif_power_data');
    }
};
