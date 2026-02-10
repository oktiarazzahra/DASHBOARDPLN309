<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarif_customer_data', function (Blueprint $table) {
            $table->integer('row_order')->default(0)->after('tarif_category');
            $table->index('row_order');
        });

        Schema::table('tarif_power_data', function (Blueprint $table) {
            $table->integer('row_order')->default(0)->after('tarif_category');
            $table->index('row_order');
        });

        Schema::table('tarif_revenue_data', function (Blueprint $table) {
            $table->integer('row_order')->default(0)->after('tarif_category');
            $table->index('row_order');
        });
    }

    public function down(): void
    {
        Schema::table('tarif_customer_data', function (Blueprint $table) {
            $table->dropIndex(['row_order']);
            $table->dropColumn('row_order');
        });

        Schema::table('tarif_power_data', function (Blueprint $table) {
            $table->dropIndex(['row_order']);
            $table->dropColumn('row_order');
        });

        Schema::table('tarif_revenue_data', function (Blueprint $table) {
            $table->dropIndex(['row_order']);
            $table->dropColumn('row_order');
        });
    }
};
