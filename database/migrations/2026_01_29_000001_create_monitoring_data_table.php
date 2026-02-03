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
        Schema::create('monitoring_data', function (Blueprint $table) {
            $table->id();
            $table->string('location')->nullable();
            $table->string('status')->nullable();
            $table->decimal('voltage', 10, 2)->nullable();
            $table->decimal('current', 10, 2)->nullable();
            $table->decimal('power', 10, 2)->nullable();
            $table->decimal('energy', 10, 2)->nullable();
            $table->string('alert_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            
            // Index untuk performance
            $table->index('status');
            $table->index('location');
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_data');
    }
};
