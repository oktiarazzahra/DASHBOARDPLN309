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

function readRange($service, $spreadsheetId, $range) {
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    return $response->getValues() ?? [];
}

// ========================
// 1. Tab RUPIAH KWH PER ULP - cek nama sheet yang benar
// ========================
echo "=== [1] RUPIAH KWH PER ULP - header ===\n";
$rows = readRange($service, $spreadsheetId, 'RUPIAH KWH PER ULP!A1:N5');
foreach ($rows as $i => $row) {
    if (!empty(array_filter($row))) echo "Row ".($i+1).": ".implode(' | ', array_slice($row,0,14))."\n";
}

// ========================
// 2. Tab PELANGGAN/TARIF - cari header 2026 (di kanan atau di bawah)
// ========================
echo "\n=== [2] PELANGGAN/TARIF - row 1-5, kolom semua ===\n";
$rows = readRange($service, $spreadsheetId, 'PELANGGAN/TARIF!A1:Z5');
foreach ($rows as $i => $row) {
    if (!empty(array_filter($row))) {
        $cells = array_map(fn($v) => $v ?: '-', $row);
        echo "Row ".($i+1)." [".count($row)."cols]: ".implode(' | ', $cells)."\n";
    }
}

// ========================
// 3. Tab DAYA/TARIF - row 1-5, kolom semua
// ========================
echo "\n=== [3] DAYA/TARIF - row 1-5, kolom semua ===\n";
$rows = readRange($service, $spreadsheetId, 'DAYA/TARIF!A1:Z5');
foreach ($rows as $i => $row) {
    if (!empty(array_filter($row))) {
        $cells = array_map(fn($v) => $v ?: '-', $row);
        echo "Row ".($i+1)." [".count($row)."cols]: ".implode(' | ', $cells)."\n";
    }
}

// ========================
// 4. Tab KWHJUAL/TARIF - row 1-5
// ========================
echo "\n=== [4] KWHJUAL/TARIF - row 1-5, kolom semua ===\n";
$rows = readRange($service, $spreadsheetId, 'KWHJUAL/TARIF!A1:Z5');
foreach ($rows as $i => $row) {
    if (!empty(array_filter($row))) {
        $cells = array_map(fn($v) => $v ?: '-', $row);
        echo "Row ".($i+1)." [".count($row)."cols]: ".implode(' | ', $cells)."\n";
    }
}

// ========================
// 5. Tab PENDAPATAN/TARIF - row 1-5
// ========================
echo "\n=== [5] PENDAPATAN/TARIF - row 1-5, kolom semua ===\n";
$rows = readRange($service, $spreadsheetId, 'PENDAPATAN/TARIF!A1:Z5');
foreach ($rows as $i => $row) {
    if (!empty(array_filter($row))) {
        $cells = array_map(fn($v) => $v ?: '-', $row);
        echo "Row ".($i+1)." [".count($row)."cols]: ".implode(' | ', $cells)."\n";
    }
}

// ========================
// 6. Tab SEMUA/TARIF B.SEL - row 1-2, cari 2026 header (lebih ke kanan)
// ========================
echo "\n=== [6] SEMUA/TARIF B.SEL - header 2026 (kolom 40-80) ===\n";
$rows = readRange($service, $spreadsheetId, 'SEMUA/TARIF B.SEL!AN1:CD2');
foreach ($rows as $i => $row) {
    if (!empty(array_filter($row))) {
        $cells = array_map(fn($v) => $v ?: '-', $row);
        $preview = implode(' | ', array_slice($cells,0,20));
        echo "Row ".($i+1).": $preview\n";
    }
}

// ========================
// 7. Cek semua col header Tab SEMUA/TARIF B.SEL baris 1 - cari yang ada tiap BULANAN
// ========================
echo "\n=== [7] SEMUA/TARIF B.SEL - scan ALL headers row 1 ===\n";
$rows = readRange($service, $spreadsheetId, 'SEMUA/TARIF B.SEL!A1:CD1');
if (!empty($rows[0])) {
    foreach ($rows[0] as $col => $val) {
        if (!empty(trim($val))) {
            echo "  Col ".($col+1)." (".chr(65+($col%26)).($col>=26?'':''). "): $val\n";
        }
    }
}
