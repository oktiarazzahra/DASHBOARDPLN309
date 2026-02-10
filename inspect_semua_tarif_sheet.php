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

echo "=== Inspecting SEMUA/TARIF B.SEL Sheet Structure ===\n\n";

// Ambil row 1-10 untuk melihat header structure
$range = 'SEMUA/TARIF B.SEL!A1:BZ10';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

echo "First 10 rows structure:\n";
echo str_repeat("=", 100) . "\n\n";

foreach ($values as $rowIndex => $row) {
    $rowNum = $rowIndex + 1;
    echo "Row {$rowNum}:\n";
    
    foreach ($row as $colIndex => $cell) {
        if (!empty($cell) && $colIndex < 60) { // Only show first 60 columns
            $colLabel = '';
            if ($colIndex < 26) {
                $colLabel = chr(65 + $colIndex);
            } else {
                $colLabel = chr(64 + floor($colIndex / 26)) . chr(65 + ($colIndex % 26));
            }
            
            echo "  Col {$colLabel} (idx {$colIndex}): {$cell}\n";
        }
    }
    echo "\n";
}

// Check for BULANAN row
echo "\n" . str_repeat("=", 100) . "\n";
echo "Looking for section headers...\n";
echo str_repeat("=", 100) . "\n\n";

foreach ($values as $rowIndex => $row) {
    $rowNum = $rowIndex + 1;
    if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
        echo "Found BULANAN at row {$rowNum}\n";
        echo "Columns in that row:\n";
        foreach ($row as $colIndex => $cell) {
            if (!empty($cell) && $colIndex < 60) {
                echo "  Col {$colIndex}: {$cell}\n";
            }
        }
    }
}
