<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Google\Client;
use Google\Service\Sheets;

echo "=== Inspecting PELANGGAN/TARIF Sheet ===\n\n";

$client = new Client();
$client->setAuthConfig(storage_path('app/google/service-account.json'));
$client->addScope(Sheets::SPREADSHEETS_READONLY);

$service = new Sheets($client);
$spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
$sheetName = 'PELANGGAN/TARIF';

// Get first 20 rows
$range = "{$sheetName}!A1:Z20";
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

if (empty($values)) {
    echo "No data found in {$sheetName}\n";
    exit;
}

echo "Sheet: {$sheetName}\n";
echo "Total rows retrieved: " . count($values) . "\n\n";

// Show structure
foreach ($values as $rowIndex => $row) {
    $rowNum = $rowIndex + 1;
    echo "Row {$rowNum}: ";
    
    // Show column labels
    $columns = [];
    foreach ($row as $colIndex => $cell) {
        $colLabel = chr(65 + $colIndex); // A, B, C, ...
        if ($colIndex >= 26) {
            $colLabel = chr(64 + floor($colIndex / 26)) . chr(65 + ($colIndex % 26));
        }
        $columns[] = "{$colLabel}={$cell}";
    }
    echo implode(' | ', $columns) . "\n";
}

echo "\n=== Checking for 2025 data ===\n";
// Look for year 2025 in the sheet
foreach ($values as $rowIndex => $row) {
    $rowNum = $rowIndex + 1;
    foreach ($row as $cell) {
        if (strpos((string)$cell, '2025') !== false) {
            echo "Found '2025' at row {$rowNum}\n";
            break;
        }
    }
}
