<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking database tables...\n\n";
    
    // Check if tarif tables exist
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- {$table->name}\n";
    }
    
    echo "\n";
    
    // Check if tarif_customer_data exists
    $hasTarifCustomer = collect($tables)->contains(fn($t) => $t->name === 'tarif_customer_data');
    $hasTarifPower = collect($tables)->contains(fn($t) => $t->name === 'tarif_power_data');
    $hasTarifRevenue = collect($tables)->contains(fn($t) => $t->name === 'tarif_revenue_data');
    
    echo "Tarif tables status:\n";
    echo "- tarif_customer_data: " . ($hasTarifCustomer ? "EXISTS" : "NOT FOUND") . "\n";
    echo "- tarif_power_data: " . ($hasTarifPower ? "EXISTS" : "NOT FOUND") . "\n";
    echo "- tarif_revenue_data: " . ($hasTarifRevenue ? "EXISTS" : "NOT FOUND") . "\n";
    
    if (!$hasTarifCustomer || !$hasTarifPower || !$hasTarifRevenue) {
        echo "\nMISSING TABLES! Please run: php artisan migrate\n";
    } else {
        echo "\nAll tarif tables exist!\n";
        
        // Check row counts
        $customerCount = DB::table('tarif_customer_data')->count();
        $powerCount = DB::table('tarif_power_data')->count();
        $revenueCount = DB::table('tarif_revenue_data')->count();
        
        echo "\nRow counts:\n";
        echo "- tarif_customer_data: {$customerCount} rows\n";
        echo "- tarif_power_data: {$powerCount} rows\n";
        echo "- tarif_revenue_data: {$revenueCount} rows\n";
        
        if ($customerCount == 0) {
            echo "\nNO DATA! Please run: php artisan sync:tarif\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
