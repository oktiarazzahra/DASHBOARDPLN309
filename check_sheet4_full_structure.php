<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$client = new Client();
$client->setApplicationName('Dashboard PLN 309');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(__DIR__ . '/storage/app/google/service-account.json');

$service = new Sheets($client);
$spreadsheetId = '1vJicMHbG0gq380G4GA1wg9HYZmgYwKdxISKQfg0m7Ek';

echo "Mengecek FULL struktur Sheet 4 untuk kWh, Rp, dan Rp/kWh...\n";
echo str_repeat("=", 100) . "\n\n";

// Ambil data sheet 4 dengan range yang lebih luas
$range = 'RUPIAH/KWH!A1:BZ30';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Fungsi convert index ke kolom Excel (A, B, C, ..., AA, AB, dst)
function indexToColumn($index) {
    $column = '';
    while ($index >= 0) {
        $column = chr(65 + ($index % 26)) . $column;
        $index = floor($index / 26) - 1;
    }
    return $column;
}

// Tampilkan header row untuk identifikasi
echo "ROW 1-10 - Mencari judul dan header:\n";
echo str_repeat("-", 100) . "\n";
for ($r = 0; $r < 10; $r++) {
    $row = $values[$r] ?? [];
    $rowNum = $r + 1;
    
    if (isset($row[0])) {
        echo "Row {$rowNum}: {$row[0]}\n";
        
        // Tampilkan kolom penting jika ada keyword
        if (stripos($row[0], 'KWH') !== false || 
            stripos($row[0], 'PENDAPATAN') !== false ||
            stripos($row[0], 'RUPIAH') !== false) {
            echo "  >>> TITLE ROW DETECTED <<<\n";
        }
    }
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "ROW 8 - BULANAN Header dengan semua kolom bulan:\n";
echo str_repeat("=", 100) . "\n";

$headerRow = $values[7] ?? []; // Row 8 (index 7)
$months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

// Scan semua kolom untuk menemukan bulan-bulan
echo "Mencari kolom bulan di Row 8:\n\n";
$monthPositions = [];
foreach ($headerRow as $colIndex => $cellValue) {
    $cellValue = strtoupper(trim($cellValue));
    if (in_array($cellValue, $months)) {
        $colName = indexToColumn($colIndex);
        echo "  Kolom {$colName} (index {$colIndex}): {$cellValue}\n";
        $monthPositions[$cellValue][] = $colIndex;
    }
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "Analisis: Posisi bulan untuk tiap data type:\n";
echo str_repeat("=", 100) . "\n";

// Identifikasi pattern: biasanya JAN muncul 3-4 kali (kWh BULANAN, Rp BULANAN, kWh KOMULATIF, Rp KOMULATIF)
if (isset($monthPositions['JAN'])) {
    echo "JAN ditemukan di index: " . implode(", ", $monthPositions['JAN']) . "\n";
    
    if (count($monthPositions['JAN']) >= 2) {
        $janPositions = $monthPositions['JAN'];
        echo "\nAsumsi struktur sheet:\n";
        echo "  - kWh JUAL BULANAN dimulai dari index: {$janPositions[0]}\n";
        echo "  - RP PENDAPATAN BULANAN dimulai dari index: {$janPositions[1]}\n";
        if (isset($janPositions[2])) {
            echo "  - Data ke-3 (mungkin Rp/kWh atau kWh KOMULATIF) dimulai dari index: {$janPositions[2]}\n";
        }
    }
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "Sample Data Row 11 (23200 ULP BPN SELATAN):\n";
echo str_repeat("=", 100) . "\n";

$dataRow = $values[10] ?? []; // Row 11 (index 10)
echo "ULP Code: " . ($dataRow[0] ?? 'N/A') . "\n";
echo "ULP Name: " . ($dataRow[1] ?? 'N/A') . "\n\n";

if (isset($monthPositions['JAN']) && count($monthPositions['JAN']) >= 2) {
    $kwhStart = $monthPositions['JAN'][0];
    $rpStart = $monthPositions['JAN'][1];
    
    echo "kWh JUAL (dimulai dari index {$kwhStart}):\n";
    for ($i = 0; $i < 12; $i++) {
        $colIndex = $kwhStart + $i;
        $value = $dataRow[$colIndex] ?? 'N/A';
        $colName = indexToColumn($colIndex);
        echo "  {$months[$i]} (col {$colName}, index {$colIndex}): {$value}\n";
    }
    
    echo "\nRP PENDAPATAN (dimulai dari index {$rpStart}):\n";
    for ($i = 0; $i < 12; $i++) {
        $colIndex = $rpStart + $i;
        $value = $dataRow[$colIndex] ?? 'N/A';
        $colName = indexToColumn($colIndex);
        echo "  {$months[$i]} (col {$colName}, index {$colIndex}): {$value}\n";
    }
    
    // Cari Rp/kWh
    if (isset($monthPositions['JAN'][2])) {
        $rpPerKwhStart = $monthPositions['JAN'][2];
        echo "\nRP/KWH atau Data Lain (dimulai dari index {$rpPerKwhStart}):\n";
        for ($i = 0; $i < 12; $i++) {
            $colIndex = $rpPerKwhStart + $i;
            $value = $dataRow[$colIndex] ?? 'N/A';
            $colName = indexToColumn($colIndex);
            echo "  {$months[$i]} (col {$colName}, index {$colIndex}): {$value}\n";
        }
    }
}

echo "\n" . str_repeat("=", 100) . "\n";
