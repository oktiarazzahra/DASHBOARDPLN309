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
        Schema::create('power_data', function (Blueprint $table) {
            $table->id();
            $table->string('ulp_code', 10)->index();
            $table->string('ulp_name', 100);
            $table->string('month', 3); // JAN, FEB, MAR, etc
            $table->integer('year')->index();
            $table->enum('data_type', ['bulanan', 'kumulatif'])->default('bulanan');
            $table->bigInteger('power_va')->default(0); // Daya dalam VA
            $table->timestamps();

            // Unique constraint untuk mencegah duplikasi
            $table->unique(['ulp_code', 'month', 'year', 'data_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_data');
    }
};
