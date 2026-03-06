<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Google\Client;
use Google\Service\Sheets;

$client = new Client();
$client->setApplicationName('Dashboard PLN');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(storage_path('app/google/service-account.json'));

$service = new Sheets($client);
$spreadsheetId = env('GOOGLE_SPREADSHEET_ID');

// ========================
// TAB 3 - RUPIAH KWH full (cari section 2026)
// ========================
echo "=== TAB 3: RUPIAH KWH PER ULP (row 30-55) ===\n";
$range = 'RUPIAH KWH PER ULP!A30:N55';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues() ?? [];
foreach ($rows as $i => $row) {
    $rowNum = $i + 30;
    $preview = implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 14)));
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: {$preview}\n";
    }
}

// ========================
// TAB 2 - DAYA PER ULP (cari 2026)
// ========================
echo "\n=== TAB 2: JUMLAH DAYA PER ULP (row 17-35) ===\n";
$range2 = 'JUMLAH DAYA PER ULP!A17:N35';
$response2 = $service->spreadsheets_values->get($spreadsheetId, $range2);
$rows2 = $response2->getValues() ?? [];
foreach ($rows2 as $i => $row) {
    $rowNum = $i + 17;
    $preview = implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 14)));
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: {$preview}\n";
    }
}

// ========================
// TAB 4 - PELANGGAN/TARIF (cari baris 2026 / lihat apakah di bawah atau di kanan)
// ========================
echo "\n=== TAB 4: PELANGGAN/TARIF (row 50-100 cari 2026) ===\n";
$range4 = 'PELANGGAN/TARIF!A50:N100';
$response4 = $service->spreadsheets_values->get($spreadsheetId, $range4);
$rows4 = $response4->getValues() ?? [];
foreach ($rows4 as $i => $row) {
    $rowNum = $i + 50;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 14))) . "\n";
        break; // Just show first hit
    }
}
// Also check column structure (right side for 2026 cols)
echo "\n--- TAB 4: Kolom N-Z row 1-5 ---\n";
$range4b = 'PELANGGAN/TARIF!N1:Z5';
$response4b = $service->spreadsheets_values->get($spreadsheetId, $range4b);
$rows4b = $response4b->getValues() ?? [];
foreach ($rows4b as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', $row)) . "\n";
    }
}

// ========================
// TAB 5 - DAYA/TARIF (cari 2026)
// ========================
echo "\n=== TAB 5: DAYA/TARIF (row 1-5) ===\n";
$range5 = 'DAYA/TARIF!A1:Z5';
$response5 = $service->spreadsheets_values->get($spreadsheetId, $range5);
$rows5 = $response5->getValues() ?? [];
foreach ($rows5 as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', array_slice($row, 0, 16))) . "\n";
    }
}
echo "--- TAB 5: Kolom N-Z row 1-5 ---\n";
$range5b = 'DAYA/TARIF!N1:Z5';
$response5b = $service->spreadsheets_values->get($spreadsheetId, $range5b);
$rows5b = $response5b->getValues() ?? [];
foreach ($rows5b as $i => $row) {
    $rowNum = $i + 1;
    if (!empty(array_filter($row))) {
        echo "Row {$rowNum}: " . implode(' | ', array_map(fn($v) => $v ?: '-', $row)) . "\n";
    }
}

// ========================
// TAB 9: SEMUA/TARIF B.SEL (cari 2026 - lebih ke kanan)
// ========================
echo "\n=== TAB 9: SEMUA/TARIF B.SEL (row 1-2, kolom semua) ===\n";
$range9 = 'SEMUA/TARIF B.SEL!A1:BZ2';
$response9 = $service->spreadsheets_values->get($spreadsheetId, $range9);
$rows9 = $response9->getValues() ?? [];
foreach ($rows9 as $i => $row) {
    $rowNum = $i + 1;
    // Filter out empty and show all
    $filtered = array_map(fn($v) => $v ?: '-', $row);
    echo "Row {$rowNum} [" . count($row) . " cols]: ";
    // Show just non-empty headers
    foreach ($filtered as $col => $val) {
        if (strpos($val, '2026') !== false || strpos($val, 'BULANAN') !== false || strpos($val, 'KWH') !== false || strpos($val, 'DAYA') !== false || strpos($val, 'PELANGGAN') !== false || strpos($val, 'PENDAPATAN') !== false) {
            echo "col" . ($col+1) . "={$val}  ";
        }
    }
    echo "\n";
}
