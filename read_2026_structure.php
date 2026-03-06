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

// ========================
// TAB 1 - Full rows untuk lihat 2026
// ========================
echo "=== TAB 1: JUMLAH PELANGGAN PER ULP (semua baris) ===\n";
$range = 'JUMLAH PELANGGAN PER ULP!A1:N35';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues() ?? [];
foreach ($rows as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 14))) . "\n";
    }
}

// ========================
// TAB 3 - RUPIAH KWH PER ULP
// ========================
echo "\n=== TAB 3: RUPIAH KWH PER ULP (semua baris) ===\n";
$range3 = 'RUPIAH KWH PER ULP!A1:N35';
$response3 = $service->spreadsheets_values->get($spreadsheetId, $range3);
$rows3 = $response3->getValues() ?? [];
foreach ($rows3 as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 14))) . "\n";
    }
}

// ========================
// TAB 4 - PELANGGAN/TARIF (cari baris 2026)
// ========================
echo "\n=== TAB 4: PELANGGAN/TARIF (cari baris/kolom 2026) ===\n";
$range4 = 'PELANGGAN/TARIF!A1:Z10';
$response4 = $service->spreadsheets_values->get($spreadsheetId, $range4);
$rows4 = $response4->getValues() ?? [];
foreach ($rows4 as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 30))) . "\n";
    }
}

// Check if tab 4 has 2026 data by looking at column headers (go further right)
echo "\n--- KOLOM 13-30 TAB 4 (cari 2026) ---\n";
$range4b = 'PELANGGAN/TARIF!M1:AH10';
$response4b = $service->spreadsheets_values->get($spreadsheetId, $range4b);
$rows4b = $response4b->getValues() ?? [];
foreach ($rows4b as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum} cols M+: " . implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 16))) . "\n";
    }
}

// ========================
// TAB 9 - SEMUA/TARIF B.SEL (cari 2026)
// ========================
echo "\n=== TAB 9: SEMUA/TARIF B.SEL (cari kolom 2026) ===\n";
$range9b = 'SEMUA/TARIF B.SEL!A1:AH5';
$response9b = $service->spreadsheets_values->get($spreadsheetId, $range9b);
$rows9b = $response9b->getValues() ?? [];
foreach ($rows9b as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', $row)) . "\n";
    }
}
