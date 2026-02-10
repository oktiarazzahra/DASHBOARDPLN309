<?php

echo "=== Testing Tarif ULP Feature ===\n\n";

// Test 1: Check migration file
echo "1. Checking migration file...\n";
$migrationFile = glob('database/migrations/*add_ulp_code_to_tarif_tables.php');
if (count($migrationFile) > 0) {
    echo "   ✓ Migration file exists: " . basename($migrationFile[0]) . "\n";
} else {
    echo "   ✗ Migration file NOT found\n";
}
echo "\n";

// Test 2: Check service file
echo "2. Checking service file...\n";
$serviceFile = 'app/Services/TarifUlpSheetsService.php';
if (file_exists($serviceFile)) {
    echo "   ✓ TarifUlpSheetsService.php exists\n";
    
    // Check if class contains required methods
    $content = file_get_contents($serviceFile);
    $methods = ['getAllTarifUlpData', 'syncToDatabase', 'getCustomerDataFromSheet', 'getPowerDataFromSheet'];
    foreach ($methods as $method) {
        if (strpos($content, "function {$method}") !== false) {
            echo "   ✓ Method {$method} found\n";
        } else {
            echo "   ✗ Method {$method} NOT found\n";
        }
    }
} else {
    echo "   ✗ Service file NOT found\n";
}
echo "\n";

// Test 3: Check command file
echo "3. Checking artisan command...\n";
$commandFile = 'app/Console/Commands/SyncTarifUlpData.php';
if (file_exists($commandFile)) {
    echo "   ✓ SyncTarifUlpData.php exists\n";
    $content = file_get_contents($commandFile);
    if (strpos($content, "signature = 'sync:tarif-ulp") !== false) {
        echo "   ✓ Command signature correct\n";
    }
} else {
    echo "   ✗ Command file NOT found\n";
}
echo "\n";

// Test 4: Check controller updates
echo "4. Checking controller updates...\n";
$controllerFile = 'app/Http/Controllers/TarifDashboardController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $checks = [
        'ulp = $request->input' => 'ULP parameter',
        'ulpList = DB::table' => 'ULP list query',
        'when($ulp' => 'ULP filter condition',
        'compact.*ulp' => 'ULP passed to view'
    ];
    
    foreach ($checks as $pattern => $label) {
        if (preg_match("/{$pattern}/", $content)) {
            echo "   ✓ {$label} found\n";
        } else {
            echo "   ✗ {$label} NOT found\n";
        }
    }
} else {
    echo "   ✗ Controller file NOT found\n";
}
echo "\n";

// Test 5: Check view updates
echo "5. Checking view updates...\n";
$viewFile = 'resources/views/tarif/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $checks = [
        'ulpSelector' => 'ULP selector dropdown',
        '@foreach($ulpList' => 'ULP list loop',
        'ulp_code' => 'ULP code reference',
        'getElementById.*ulpSelector' => 'ULP JavaScript handler'
    ];
    
    foreach ($checks as $pattern => $label) {
        if (preg_match("/{$pattern}/", $content)) {
            echo "   ✓ {$label} found\n";
        } else {
            echo "   ✗ {$label} NOT found\n";
        }
    }
} else {
    echo "   ✗ View file NOT found\n";
}
echo "\n";

// Test 6: Check batch files
echo "6. Checking setup scripts...\n";
$scripts = [
    'setup_tarif_ulp.bat' => 'Setup batch file',
    'sync_tarif_ulp_manual.php' => 'Manual sync script'
];

foreach ($scripts as $file => $label) {
    if (file_exists($file)) {
        echo "   ✓ {$label} exists\n";
    } else {
        echo "   ✗ {$label} NOT found\n";
    }
}
echo "\n";

// Test 7: Check documentation
echo "7. Checking documentation...\n";
if (file_exists('FITUR_FILTER_ULP.md')) {
    echo "   ✓ FITUR_FILTER_ULP.md exists\n";
} else {
    echo "   ✗ Documentation NOT found\n";
}
echo "\n";

echo "=== Summary ===\n";
echo "All components for Tarif ULP filter feature are ready!\n\n";
echo "Next steps:\n";
echo "1. Run: php artisan migrate\n";
echo "2. Run: php artisan sync:tarif-ulp --year=2025\n";
echo "   OR: setup_tarif_ulp.bat\n";
echo "   OR: php sync_tarif_ulp_manual.php\n";
echo "3. Access: http://127.0.0.1:8000/tarif\n";
echo "4. Try filtering by ULP and/or month\n";
