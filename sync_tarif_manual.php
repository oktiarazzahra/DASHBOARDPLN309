<?php

echo "=== SYNC TARIF DATA ===\n\n";

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TarifCustomerSheetsService;
use App\Services\TarifPowerSheetsService;
use App\Services\TarifRevenueSheetsService;
use Illuminate\Support\Facades\DB;

$year = 2025;

try {
    echo "Syncing tarif data for year {$year}...\n\n";
    
    // Sync Customer Data
    echo "1. Syncing customer data...\n";
    $customerService = new TarifCustomerSheetsService();
    $customerData = $customerService->getCustomerData($year);
    
    if (!empty($customerData)) {
        DB::table('tarif_customer_data')->where('year', $year)->delete();
        DB::table('tarif_customer_data')->insert($customerData);
        echo "   ✓ Synced " . count($customerData) . " customer records\n\n";
    } else {
        echo "   ⚠ No customer data found\n\n";
    }
    
    // Sync Power Data
    echo "2. Syncing power data...\n";
    $powerService = new TarifPowerSheetsService();
    $powerData = $powerService->getPowerData($year);
    
    if (!empty($powerData)) {
        DB::table('tarif_power_data')->where('year', $year)->delete();
        DB::table('tarif_power_data')->insert($powerData);
        echo "   ✓ Synced " . count($powerData) . " power records\n\n";
    } else {
        echo "   ⚠ No power data found\n\n";
    }
    
    // Sync Revenue Data
    echo "3. Syncing revenue data...\n";
    $revenueService = new TarifRevenueSheetsService();
    $revenueData = $revenueService->getRevenueData($year);
    
    if (!empty($revenueData)) {
        DB::table('tarif_revenue_data')->where('year', $year)->delete();
        
        // Insert in smaller chunks to avoid SQL length issues
        $chunks = array_chunk($revenueData, 100);
        $totalInserted = 0;
        foreach ($chunks as $chunk) {
            DB::table('tarif_revenue_data')->insert($chunk);
            $totalInserted += count($chunk);
        }
        
        echo "   ✓ Synced " . $totalInserted . " revenue records\n\n";
    } else {
        echo "   ⚠ No revenue data found\n\n";
    }
    
    echo "=== SYNC COMPLETED ===\n";
    echo "\nYou can now access:\n";
    echo "- Dashboard Tarif: http://127.0.0.1:8000/tarif\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
