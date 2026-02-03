<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Google\Client;
use Google\Service\Sheets;

$spreadsheetId = config('google.sheets.spreadsheet_id');
$sheetName = 'RUPIAH/KWH'; // Sheet 4 name

$client = new Client();
$client->setApplicationName('Dashboard PLN 309');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(storage_path('app/google/service-account.json'));

$service = new Sheets($client);

echo "=== ANALISIS STRUKTUR SHEET 4: RUPIAH/KWH ===\n\n";

try {
    // Get all data from sheet
    $range = $sheetName . '!A1:BZ50';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
    
    echo "Total rows: " . count($values) . "\n\n";
    
    // Find all BULANAN and KOMULATIF sections
    echo "=== MENCARI SECTION HEADERS ===\n";
    foreach ($values as $rowIndex => $row) {
        $rowNum = $rowIndex + 1;
        
        // Check column A for BULANAN or KOMULATIF
        if (isset($row[0])) {
            $cellValue = strtoupper(trim($row[0]));
            if ($cellValue === 'BULANAN' || $cellValue === 'KOMULATIF') {
                echo "Row {$rowNum}, Col A: {$cellValue}\n";
                
                // Check what's in the next row (should be months)
                if (isset($values[$rowIndex + 1])) {
                    $monthRow = $values[$rowIndex + 1];
                    echo "  Month row: ";
                    for ($i = 1; $i <= 12; $i++) {
                        if (isset($monthRow[$i])) {
                            echo $monthRow[$i] . " ";
                        }
                    }
                    echo "\n";
                    
                    // Check for section title above
                    if ($rowIndex > 0 && isset($values[$rowIndex - 1][0])) {
                        $title = trim($values[$rowIndex - 1][0]);
                        if (!empty($title)) {
                            echo "  Section title: {$title}\n";
                        }
                    }
                }
                echo "\n";
            }
        }
    }
    
    // Find column positions for different data types
    echo "\n=== MENCARI POSISI KOLOM ===\n";
    
    // Search for title rows
    foreach ($values as $rowIndex => $row) {
        $rowNum = $rowIndex + 1;
        
        // Look for titles containing KWH, PENDAPATAN, or RP/KWH
        for ($colIndex = 0; $colIndex < count($row); $colIndex++) {
            $cellValue = isset($row[$colIndex]) ? trim($row[$colIndex]) : '';
            
            if (stripos($cellValue, 'KWH JUAL') !== false && stripos($cellValue, 'TOTAL BULANAN') !== false) {
                $colLetter = getColumnLetter($colIndex);
                echo "Row {$rowNum}, Col {$colLetter}: KWH JUAL (TOTAL BULANAN) header\n";
            }
            
            if (stripos($cellValue, 'RP PENDAPATAN') !== false && stripos($cellValue, 'TOTAL BULANAN') !== false) {
                $colLetter = getColumnLetter($colIndex);
                echo "Row {$rowNum}, Col {$colLetter}: RP PENDAPATAN (TOTAL BULANAN) header\n";
            }
            
            if (stripos($cellValue, 'RP/KWH') !== false && stripos($cellValue, 'TOTAL BULANAN') !== false) {
                $colLetter = getColumnLetter($colIndex);
                echo "Row {$rowNum}, Col {$colLetter}: RP/KWH (TOTAL BULANAN) header\n";
            }
        }
    }
    
    // Sample data from first ULP
    echo "\n=== SAMPLE DATA ULP PERTAMA ===\n";
    foreach ($values as $rowIndex => $row) {
        if (isset($row[0]) && is_numeric(trim($row[0]))) {
            $rowNum = $rowIndex + 1;
            $ulpCode = trim($row[0]);
            $ulpName = isset($row[1]) ? trim($row[1]) : '';
            
            echo "Row {$rowNum}: ULP {$ulpCode} - {$ulpName}\n";
            echo "  Kolom B (index 1): " . (isset($row[1]) ? $row[1] : 'empty') . "\n";
            echo "  Kolom C (index 2): " . (isset($row[2]) ? $row[2] : 'empty') . "\n";
            echo "  Kolom D (index 3): " . (isset($row[3]) ? $row[3] : 'empty') . "\n";
            
            // Show all non-empty columns
            echo "  All columns: ";
            for ($i = 0; $i < min(30, count($row)); $i++) {
                if (!empty($row[$i])) {
                    echo "[$i]=" . $row[$i] . " ";
                }
            }
            echo "\n\n";
            
            break; // Only show first ULP
        }
    }
    
    // Show structure around row 8-12 (where BULANAN usually is)
    echo "\n=== STRUKTUR ROW 1-15 ===\n";
    for ($i = 0; $i < min(15, count($values)); $i++) {
        $rowNum = $i + 1;
        $row = $values[$i];
        echo "Row {$rowNum}:\n";
        echo "  A (0): " . (isset($row[0]) ? substr($row[0], 0, 50) : 'empty') . "\n";
        
        // Show columns with data
        $nonEmptyCols = [];
        for ($j = 0; $j < count($row); $j++) {
            if (!empty($row[$j]) && $j > 0) {
                $nonEmptyCols[] = "[$j]=" . substr($row[$j], 0, 20);
            }
        }
        if (!empty($nonEmptyCols)) {
            echo "  Other cols: " . implode(", ", array_slice($nonEmptyCols, 0, 10)) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

function getColumnLetter($colIndex) {
    $letter = '';
    while ($colIndex >= 0) {
        $letter = chr($colIndex % 26 + 65) . $letter;
        $colIndex = floor($colIndex / 26) - 1;
    }
    return $letter;
}
