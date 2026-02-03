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
    echo "=== ULP ROWS AFTER HEADER ===\n\n";
    
    // Show next 8 rows after header (should be 6 ULP + UP3 total + empty)
    for ($i = 0; $i < 10; $i++) {
        $rowIndex = $bulananIndex + 2 + $i;
        $row = $values[$rowIndex] ?? [];
        $ulpCode = $row[0] ?? '';
        $ulpName = $row[1] ?? '';
        
        // Show Rp Pendapatan for SEP-DEC
        $sep = $row[26] ?? 'EMPTY';
        $oct = $row[27] ?? 'EMPTY';
        $nov = $row[28] ?? 'EMPTY';
        $dec = $row[29] ?? 'EMPTY';
        
        echo sprintf(
            "Row %2d: Code=%-15s Name=%-20s | SEP=%15s OCT=%15s NOV=%15s DEC=%15s\n",
            $rowIndex + 1,
            $ulpCode,
            $ulpName,
            $sep,
            $oct,
            $nov,
            $dec
        );
    }
}
