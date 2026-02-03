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

echo "Mengecek struktur Sheet 4 (RUPIAH/KWH) untuk Rp Pendapatan...\n";
echo str_repeat("=", 80) . "\n\n";

// Ambil data dari sheet RUPIAH/KWH
$range = 'RUPIAH/KWH!A1:AZ30';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Cari row BULANAN dan tampilkan struktur kolom Rp Pendapatan
foreach ($values as $rowIndex => $row) {
    $rowNum = $rowIndex + 1;
    
    // Tampilkan row 1-20 untuk analisis
    if ($rowNum <= 20) {
        if (isset($row[0])) {
            echo "Row {$rowNum}: " . $row[0];
            
            // Jika ada BULANAN atau header penting, tampilkan lebih detail
            if (stripos($row[0], 'BULANAN') !== false || 
                stripos($row[0], 'KOMULATIF') !== false ||
                stripos($row[0], 'RP PENDAPATAN') !== false) {
                echo " (HEADER DETECTED)";
            }
            echo "\n";
            
            // Jika ini row UP3 atau ULP, tampilkan kolom Rp Pendapatan (sekitar kolom 18-29)
            if ($rowNum >= 10 && $rowNum <= 17) {
                echo "  - Kolom S (index 18) JAN: " . ($row[18] ?? 'N/A') . "\n";
                echo "  - Kolom T (index 19) FEB: " . ($row[19] ?? 'N/A') . "\n";
                echo "  - Kolom U (index 20) MAR: " . ($row[20] ?? 'N/A') . "\n";
                echo "  - Kolom AD (index 29) DEC: " . ($row[29] ?? 'N/A') . "\n";
            }
        }
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Analisis Kolom Rp Pendapatan:\n";
echo str_repeat("=", 80) . "\n";

// Ambil data row 11 (23200 ULP BPN SELATAN) untuk sample
$sampleRow = $values[10] ?? []; // Row 11 (index 10)
echo "Sample data Row 11 (23200 ULP BPN SELATAN):\n";
echo "ULP Code: " . ($sampleRow[0] ?? 'N/A') . "\n";
echo "ULP Name: " . ($sampleRow[1] ?? 'N/A') . "\n";
echo "\nRp Pendapatan (Kolom S-AD, index 18-29):\n";
$months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
for ($i = 0; $i < 12; $i++) {
    $colIndex = 18 + $i;
    $value = $sampleRow[$colIndex] ?? 'N/A';
    echo "  {$months[$i]} (col " . chr(65 + $colIndex) . ", index {$colIndex}): {$value}\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Verifikasi: Apakah data Rp Pendapatan sudah di kolom yang benar?\n";
echo "Kolom yang digunakan di kode: index 18-29 (S-AD)\n";
echo str_repeat("=", 80) . "\n";
