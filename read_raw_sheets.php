<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Google\Client;
use Google\Service\Sheets;

$config = config('google');
$client = new Client();
$client->setApplicationName('Dashboard PLN');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(storage_path('app/google/service-account.json'));

$service = new Sheets($client);
$spreadsheetId = env('GOOGLE_SPREADSHEET_ID');

// Baca semua sheet names
$spreadsheet = $service->spreadsheets->get($spreadsheetId);
$sheets = $spreadsheet->getSheets();

echo "=== DAFTAR TAB SPREADSHEET ===\n";
foreach ($sheets as $i => $sheet) {
    echo ($i+1) . ". " . $sheet->getProperties()->getTitle() . "\n";
}

echo "\n=== BACA TAB 1: JUMLAH PELANGGAN PER ULP ===\n";
$sheetName = 'JUMLAH PELANGGAN PER ULP';
$range = $sheetName . '!A1:Z30';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues() ?? [];

foreach ($rows as $i => $row) {
    $rowNum = $i + 1;
    $preview = implode(' | ', array_slice($row, 0, 20));
    if (!empty(trim($preview))) {
        echo "Row {$rowNum}: {$preview}\n";
    }
}

echo "\n=== BACA TAB 2: JUMLAH DAYA PER ULP (baris 1-20) ===\n";
$sheetName2 = 'JUMLAH DAYA PER ULP';
$range2 = $sheetName2 . '!A1:Z20';
$response2 = $service->spreadsheets_values->get($spreadsheetId, $range2);
$rows2 = $response2->getValues() ?? [];

foreach ($rows2 as $i => $row) {
    $rowNum = $i + 1;
    $preview = implode(' | ', array_slice($row, 0, 20));
    if (!empty(trim($preview))) {
        echo "Row {$rowNum}: {$preview}\n";
    }
}

echo "\n=== BACA TAB 4: PELANGGAN/TARIF (baris 1-10) ===\n";
$sheetName4 = 'PELANGGAN/TARIF';
$range4 = $sheetName4 . '!A1:Z10';
$response4 = $service->spreadsheets_values->get($spreadsheetId, $range4);
$rows4 = $response4->getValues() ?? [];

foreach ($rows4 as $i => $row) {
    $rowNum = $i + 1;
    $preview = implode(' | ', array_slice($row, 0, 20));
    if (!empty(trim($preview))) {
        echo "Row {$rowNum}: {$preview}\n";
    }
}

echo "\n=== BACA TAB 9: SEMUA/TARIF B.SEL (baris 1-10) ===\n";
$sheetName9 = 'SEMUA/TARIF B.SEL';
$range9 = $sheetName9 . '!A1:Z10';
$response9 = $service->spreadsheets_values->get($spreadsheetId, $range9);
$rows9 = $response9->getValues() ?? [];

foreach ($rows9 as $i => $row) {
    $rowNum = $i + 1;
    $preview = implode(' | ', array_slice($row, 0, 20));
    if (!empty(trim($preview))) {
        echo "Row {$rowNum}: {$preview}\n";
    }
}
