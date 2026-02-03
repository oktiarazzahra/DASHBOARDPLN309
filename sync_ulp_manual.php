<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CustomerSheetsService;
use App\Services\PowerSheetsService;
use App\Services\RevenueSheetsService;
use Illuminate\Support\Facades\DB;

$year = 2025;

echo "Starting ULP data sync for year {$year}...\n\n";

// Sync Customer Data
echo "Syncing customer data...\n";
$customerService = new CustomerSheetsService();
$result = $customerService->syncToDatabase();
echo "✓ Customer data synced\n";

// Sync Power Data
echo "\nSyncing power data...\n";
$powerService = new PowerSheetsService();
$result = $powerService->syncToDatabase();
echo "✓ Power data synced\n";

// Sync Revenue Data
echo "\nSyncing revenue data...\n";
$revenueService = new RevenueSheetsService();
$result = $revenueService->syncToDatabase();
echo "✓ Revenue data synced\n";

echo "\n✓ ULP data sync completed!\n";
echo "Refresh your browser now!\n";
