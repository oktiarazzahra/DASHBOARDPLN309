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

$spreadsheet = $service->spreadsheets->get($spreadsheetId);
$sheets = $spreadsheet->getSheets();

echo "Available sheets:\n";
foreach ($sheets as $index => $sheet) {
    echo ($index + 1) . ". " . $sheet->getProperties()->getTitle() . "\n";
}

// Check sheet 3 data
echo "\n--- Checking Sheet 3 Data ---\n";
$sheet3 = $sheets[2]; // 0-indexed
$sheet3Name = $sheet3->getProperties()->getTitle();
echo "Sheet 3 name: " . $sheet3Name . "\n\n";

// Get first 20 rows to see structure
$range = $sheet3Name . '!A1:N20';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

foreach ($values as $rowIndex => $row) {
    echo "Row " . ($rowIndex + 1) . ": ";
    if (isset($row[0])) {
        echo $row[0];
        if (isset($row[1])) echo " | " . $row[1];
        if (isset($row[2])) echo " | " . $row[2];
    }
    echo "\n";
}
