<?php

echo "=== SYNC TARIF PER ULP MANUAL ===\n\n";

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TarifUlpSheetsService;
use Illuminate\Support\Facades\DB;

$year = 2025;

try {
    echo "Syncing tarif per ULP data for year {$year}...\n\n";
    
    // Delete existing ULP data
    echo "Deleting existing ULP data for year {$year}...\n";
    DB::table('tarif_customer_data')->where('year', $year)->whereNotNull('ulp_code')->delete();
    DB::table('tarif_power_data')->where('year', $year)->whereNotNull('ulp_code')->delete();
    DB::table('tarif_revenue_data')->where('year', $year)->whereNotNull('ulp_code')->delete();
    echo "✓ Old data deleted\n\n";
    
    // Sync new data
    echo "Fetching data from Google Sheets...\n";
    $service = new TarifUlpSheetsService();
    $results = $service->syncToDatabase($year);
    
    echo "\n=== Sync Completed ===\n";
    echo "✓ Customer records: " . $results['customer'] . "\n";
    echo "✓ Power records: " . $results['power'] . "\n";
    echo "✓ kWh records: " . $results['kwh'] . "\n";
    echo "✓ Rp records: " . $results['rp'] . "\n\n";
    
    echo "Total records synced: " . array_sum($results) . "\n";
    echo "✓ Sync completed successfully!\n";
    echo "Refresh your browser now!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
