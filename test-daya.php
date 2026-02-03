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
    $sheetName = 'JUMLAH DAYA PER ULP';
    $range = $sheetName . '!A1:Z100';
    
    echo "Testing JUMLAH DAYA PER ULP Sheet...\n";
    echo "Spreadsheet ID: $spreadsheetId\n";
    echo "Sheet Name: $sheetName\n";
    echo "Range: $range\n\n";
    
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
    
    echo "Total rows: " . count($values) . "\n\n";
    
    // Show first 20 rows to understand structure
    echo "First 20 rows:\n";
    echo str_repeat("=", 100) . "\n";
    foreach (array_slice($values, 0, 20) as $index => $row) {
        $firstCol = isset($row[0]) ? $row[0] : '';
        $secondCol = isset($row[1]) ? $row[1] : '';
        $thirdCol = isset($row[2]) ? $row[2] : '';
        
        echo sprintf("Row %2d: [0]='%s' [1]='%s' [2]='%s'\n", 
            $index, 
            substr($firstCol, 0, 30),
            substr($secondCol, 0, 30),
            substr($thirdCol, 0, 30)
        );
        
        // Show full row if it contains BULANAN or KOMULATIF
        if (stripos($firstCol, 'BULANAN') !== false || stripos($firstCol, 'KOMULATIF') !== false) {
            echo "   FULL ROW: " . json_encode($row) . "\n";
        }
    }
    
    echo "\n" . str_repeat("=", 100) . "\n\n";
    
    // Find BULANAN section
    foreach ($values as $index => $row) {
        $firstCol = isset($row[0]) ? trim($row[0]) : '';
        
        if (strtoupper($firstCol) === 'BULANAN') {
            echo "Found BULANAN at row $index\n";
            echo "Row $index: " . json_encode($row) . "\n";
            echo "Row " . ($index + 1) . " (Header): " . json_encode($values[$index + 1] ?? []) . "\n";
            echo "Row " . ($index + 2) . " (Data 1): " . json_encode($values[$index + 2] ?? []) . "\n";
            echo "Row " . ($index + 3) . " (Data 2): " . json_encode($values[$index + 3] ?? []) . "\n";
            echo "\n";
            break;
        }
    }
    
    // Find KOMULATIF section
    foreach ($values as $index => $row) {
        $firstCol = isset($row[0]) ? trim($row[0]) : '';
        
        if (strtoupper($firstCol) === 'KOMULATIF') {
            echo "Found KOMULATIF at row $index\n";
            echo "Row $index: " . json_encode($row) . "\n";
            echo "Row " . ($index + 1) . " (Header): " . json_encode($values[$index + 1] ?? []) . "\n";
            echo "Row " . ($index + 2) . " (Data 1): " . json_encode($values[$index + 2] ?? []) . "\n";
            echo "\n";
            break;
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
