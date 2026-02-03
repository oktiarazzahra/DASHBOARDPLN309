<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$serviceAccountPath = storage_path('app/google/service-account.json');

try {
    $client = new Client();
    $client->setApplicationName('Dashboard PLN 309');
    $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
    $client->setAuthConfig($serviceAccountPath);
    
    $service = new Sheets($client);
    $spreadsheetId = config('google.sheets.spreadsheet_id');
    $sheetName = 'JUMLAH PELANGGAN PER ULP'; // Hardcode sheet name
    $range = $sheetName . '!A1:Z100';
    
    echo "Testing Data Fetch...\n";
    echo "Spreadsheet ID: $spreadsheetId\n";
    echo "Sheet Name: $sheetName\n";
    echo "Range: $range\n\n";
    
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
    
    echo "Total rows: " . count($values) . "\n\n";
    
    // Cari row BULANAN
    foreach ($values as $index => $row) {
        $firstCol = isset($row[0]) ? trim($row[0]) : '';
        
        if (strtoupper($firstCol) === 'BULANAN') {
            echo "Found BULANAN at row $index\n";
            echo "Row " . ($index) . ": " . json_encode($row) . "\n";
            echo "Row " . ($index + 1) . " (Header bulan): " . json_encode($values[$index + 1] ?? []) . "\n";
            echo "Row " . ($index + 2) . " (Data pertama): " . json_encode($values[$index + 2] ?? []) . "\n";
            echo "Row " . ($index + 3) . " (Data kedua): " . json_encode($values[$index + 3] ?? []) . "\n";
            echo "\n";
            break;
        }
    }
    
    // Cari row KOMULATIF
    foreach ($values as $index => $row) {
        $firstCol = isset($row[0]) ? trim($row[0]) : '';
        
        if (strtoupper($firstCol) === 'KOMULATIF') {
            echo "Found KOMULATIF at row $index\n";
            echo "Row " . ($index) . ": " . json_encode($row) . "\n";
            echo "Row " . ($index + 1) . " (Header bulan): " . json_encode($values[$index + 1] ?? []) . "\n";
            echo "Row " . ($index + 2) . " (Data pertama): " . json_encode($values[$index + 2] ?? []) . "\n";
            echo "\n";
            break;
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
