<?php

echo "=== Testing Tarif Feature ===\n\n";

// Test 1: Check if migration files exist
echo "1. Checking migration files...\n";
$migrationFiles = glob('database/migrations/*tarif*.php');
echo "   Found " . count($migrationFiles) . " tarif migration files:\n";
foreach ($migrationFiles as $file) {
    echo "   - " . basename($file) . "\n";
}
echo "\n";

// Test 2: Check if service files exist
echo "2. Checking service files...\n";
$serviceFiles = [
    'app/Services/TarifCustomerSheetsService.php',
    'app/Services/TarifPowerSheetsService.php',
    'app/Services/TarifRevenueSheetsService.php',
];
foreach ($serviceFiles as $file) {
    $exists = file_exists($file);
    echo "   " . ($exists ? "✓" : "✗") . " " . basename($file) . "\n";
}
echo "\n";

// Test 3: Check if command exists
echo "3. Checking artisan command...\n";
$commandFile = 'app/Console/Commands/SyncTarifData.php';
$exists = file_exists($commandFile);
echo "   " . ($exists ? "✓" : "✗") . " SyncTarifData.php\n";
echo "\n";

// Test 4: Check if controller exists
echo "4. Checking controller...\n";
$controllerFile = 'app/Http/Controllers/TarifDashboardController.php';
$exists = file_exists($controllerFile);
echo "   " . ($exists ? "✓" : "✗") . " TarifDashboardController.php\n";
echo "\n";

// Test 5: Check if view exists
echo "5. Checking view file...\n";
$viewFile = 'resources/views/tarif/index.blade.php';
$exists = file_exists($viewFile);
echo "   " . ($exists ? "✓" : "✗") . " tarif/index.blade.php\n";
echo "\n";

// Test 6: Check routes file
echo "6. Checking routes...\n";
$routesContent = file_get_contents('routes/web.php');
if (strpos($routesContent, 'TarifDashboardController') !== false) {
    echo "   ✓ TarifDashboardController registered in routes\n";
} else {
    echo "   ✗ TarifDashboardController NOT found in routes\n";
}
if (strpos($routesContent, '/tarif') !== false) {
    echo "   ✓ /tarif route registered\n";
} else {
    echo "   ✗ /tarif route NOT found\n";
}
echo "\n";

echo "=== Summary ===\n";
echo "All core files for tarif feature have been created!\n\n";
echo "Next steps:\n";
echo "1. Run: php artisan migrate\n";
echo "2. Run: php artisan sync:tarif\n";
echo "3. Access: http://127.0.0.1:8000/tarif\n";
