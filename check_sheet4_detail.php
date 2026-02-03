<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Google\Client;
use Google\Service\Sheets;

$spreadsheetId = config('google.sheets.spreadsheet_id');
$sheetName = 'RUPIAH/KWH';

$client = new Client();
$client->setApplicationName('Dashboard PLN 309');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(storage_path('app/google/service-account.json'));

$service = new Sheets($client);

// Ambil baris header dan beberapa baris data
$range = $sheetName . '!A1:AZ100';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

echo "=== CHECKING SHEET 4 STRUCTURE ===\n\n";

// Cari baris BULANAN
$bulananIndex = null;
foreach ($values as $index => $row) {
    if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
        $bulananIndex = $index;
        echo "Found BULANAN at row " . ($index + 1) . "\n\n";
        break;
    }
}

if ($bulananIndex) {
    // Show header row (BULANAN row + 1)
    $headerRow = $values[$bulananIndex + 1] ?? [];
    echo "Header row (row " . ($bulananIndex + 2) . "):\n";
    foreach ($headerRow as $colIndex => $cell) {
        if ($colIndex >= 0 && $colIndex <= 45) {
            $colLetter = chr(65 + $colIndex);
            if ($colIndex > 25) $colLetter = 'A' . chr(65 + ($colIndex - 26));
            echo sprintf("Col %2d (%2s): %s\n", $colIndex, $colLetter, $cell);
        }
    }
    
    echo "\n=== FIRST ULP DATA (row " . ($bulananIndex + 3) . ") ===\n";
    $firstDataRow = $values[$bulananIndex + 2] ?? [];
    echo "ULP Code: " . ($firstDataRow[0] ?? 'N/A') . "\n";
    echo "ULP Name: " . ($firstDataRow[1] ?? 'N/A') . "\n\n";
    
    echo "kWh Jual (columns 2-13):\n";
    for ($i = 2; $i <= 13; $i++) {
        $month = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'][$i - 2];
        $value = $firstDataRow[$i] ?? 'EMPTY';
        echo sprintf("  %s (col %2d): %s\n", $month, $i, $value);
    }
    
    echo "\nRp Pendapatan (columns 18-29):\n";
    for ($i = 18; $i <= 29; $i++) {
        $month = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'][$i - 18];
        $value = $firstDataRow[$i] ?? 'EMPTY';
        $colLetter = chr(65 + $i);
        if ($i > 25) $colLetter = 'A' . chr(65 + ($i - 26));
        echo sprintf("  %s (col %2d / %2s): %s\n", $month, $i, $colLetter, $value);
    }
    
    echo "\nRp/kWh (columns 34-45):\n";
    for ($i = 34; $i <= 45; $i++) {
        $month = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'][$i - 34];
        $value = $firstDataRow[$i] ?? 'EMPTY';
        $colLetter = '';
        if ($i <= 25) {
            $colLetter = chr(65 + $i);
        } else {
            $colLetter = 'A' . chr(65 + ($i - 26));
        }
        echo sprintf("  %s (col %2d / %2s): %s\n", $month, $i, $colLetter, $value);
    }
}
