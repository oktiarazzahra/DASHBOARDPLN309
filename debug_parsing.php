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

$range = $sheetName . '!A1:AZ100';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Cari BULANAN
$bulananIndex = null;
foreach ($values as $index => $row) {
    if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
        $bulananIndex = $index;
        break;
    }
}

if ($bulananIndex) {
    // Ambil data ULP pertama (23200)
    $ulpRowIndex = $bulananIndex + 3; // Skip header, skip UP3 total
    $row = $values[$ulpRowIndex];
    
    echo "=== ULP 23200 DATA ===\n";
    echo "Row: " . ($ulpRowIndex + 1) . "\n";
    echo "ULP Code: " . ($row[0] ?? 'N/A') . "\n";
    echo "ULP Name: " . ($row[1] ?? 'N/A') . "\n\n";
    
    $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
    
    echo "Rp Pendapatan with parsing:\n";
    for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
        $rpColumn = $monthIndex + 18;
        $rawValue = $row[$rpColumn] ?? 'EMPTY';
        $parsed = isset($row[$rpColumn]) ? 
            (int)str_replace([',', '.'], '', trim($row[$rpColumn])) : 0;
        
        echo sprintf(
            "%s: col=%2d raw=%-20s parsed=%s\n",
            $months[$monthIndex],
            $rpColumn,
            $rawValue,
            number_format($parsed)
        );
    }
    
    echo "\n\nDEBUG: Check if values exist:\n";
    echo "Index 18: " . (isset($row[18]) ? 'EXISTS' : 'NOT EXISTS') . " = " . ($row[18] ?? 'N/A') . "\n";
    echo "Index 26: " . (isset($row[26]) ? 'EXISTS' : 'NOT EXISTS') . " = " . ($row[26] ?? 'N/A') . "\n";
    echo "Index 27: " . (isset($row[27]) ? 'EXISTS' : 'NOT EXISTS') . " = " . ($row[27] ?? 'N/A') . "\n";
    echo "Index 28: " . (isset($row[28]) ? 'EXISTS' : 'NOT EXISTS') . " = " . ($row[28] ?? 'N/A') . "\n";
    echo "Index 29: " . (isset($row[29]) ? 'EXISTS' : 'NOT EXISTS') . " = " . ($row[29] ?? 'N/A') . "\n";
    
    echo "\nTotal columns in row: " . count($row) . "\n";
}
