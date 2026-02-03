<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Google\Client;
use Google\Service\Sheets;

$client = new Client();
$client->setApplicationName('Dashboard PLN 309');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(storage_path('app/google/service-account.json'));

$service = new Sheets($client);
$spreadsheetId = config('google.sheets.spreadsheet_id');
$sheetName = 'JUMLAH PELANGGAN PER ULP';

// Get data
$range = $sheetName . '!A1:N20';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

echo "=== Analyzing Customer Sheet Structure ===\n\n";

// Find BULANAN row
foreach ($values as $index => $row) {
    if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
        echo "BULANAN found at row " . ($index + 1) . " (index $index)\n\n";
        
        // Show next few rows
        for ($i = $index; $i < min($index + 8, count($values)); $i++) {
            $r = $values[$i];
            echo "Row " . ($i + 1) . ": ";
            echo "A=" . ($r[0] ?? 'empty') . " | ";
            echo "B=" . ($r[1] ?? 'empty') . " | ";
            echo "C=" . ($r[2] ?? 'empty') . " | ";
            echo "D=" . ($r[3] ?? 'empty');
            echo "\n";
        }
        break;
    }
}

// Test number parsing
echo "\n=== Testing Number Parsing ===\n";
$testNumbers = ['471.82', '471,82', '471.820', '471,820'];
foreach ($testNumbers as $num) {
    echo "Original: '$num' => ";
    echo "Remove comma/dot: '" . str_replace([',', '.'], '', trim($num)) . "' => ";
    
    // Better approach: replace thousand separator first
    $cleaned = str_replace([',', '.'], '', trim($num));
    echo "Result: $cleaned\n";
}
