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
$sheetName = 'JUMLAH DAYA PER ULP';

// Get data
$range = $sheetName . '!A1:N25';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

echo "=== Analyzing Sheet Structure ===\n\n";

// Find BULANAN row
$headerRowIndex = null;
foreach ($values as $index => $row) {
    if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
        $headerRowIndex = $index;
        echo "BULANAN found at row " . ($index + 1) . " (index $index)\n";
        break;
    }
}

if ($headerRowIndex === null) {
    echo "ERROR: BULANAN header not found!\n";
    exit;
}

// Show structure around BULANAN
echo "\nRows around BULANAN:\n";
for ($i = $headerRowIndex; $i < min($headerRowIndex + 10, count($values)); $i++) {
    $row = $values[$i];
    echo "Row " . ($i + 1) . " (index $i): ";
    echo "A=" . ($row[0] ?? 'empty') . " | ";
    echo "B=" . ($row[1] ?? 'empty') . " | ";
    echo "C=" . ($row[2] ?? 'empty') . " | ";
    echo "D=" . ($row[3] ?? 'empty');
    echo "\n";
}

// Check where months start
echo "\n=== Looking for month headers ===\n";
$monthRowIndex = $headerRowIndex + 1;
$monthRow = $values[$monthRowIndex];
echo "Row " . ($monthRowIndex + 1) . " content:\n";
for ($i = 0; $i < min(15, count($monthRow)); $i++) {
    echo "  Column " . chr(65 + $i) . " (index $i): " . ($monthRow[$i] ?? 'empty') . "\n";
}

// Find where JAN starts
$janColumnIndex = null;
foreach ($monthRow as $colIdx => $cell) {
    if (isset($cell) && strtoupper(trim($cell)) === 'JAN') {
        $janColumnIndex = $colIdx;
        echo "\nJAN found at column " . chr(65 + $colIdx) . " (index $colIdx)\n";
        break;
    }
}

if ($janColumnIndex === null) {
    echo "\nERROR: JAN not found in month row!\n";
    // Try next row
    $monthRowIndex = $headerRowIndex + 2;
    $monthRow = $values[$monthRowIndex];
    echo "Trying row " . ($monthRowIndex + 1) . ":\n";
    foreach ($monthRow as $colIdx => $cell) {
        if (isset($cell) && strtoupper(trim($cell)) === 'JAN') {
            $janColumnIndex = $colIdx;
            echo "JAN found at column " . chr(65 + $colIdx) . " (index $colIdx)\n";
            break;
        }
    }
}

// Show data rows
echo "\n=== Data Rows ===\n";
$dataStartRow = $monthRowIndex + 1;
for ($i = $dataStartRow; $i < min($dataStartRow + 8, count($values)); $i++) {
    $row = $values[$i];
    if (empty($row[0])) break;
    
    $ulpCode = $row[0] ?? '';
    $ulpName = $row[1] ?? '';
    echo "\nRow " . ($i + 1) . ": $ulpCode | $ulpName\n";
    
    if ($janColumnIndex !== null) {
        echo "  JAN (col " . chr(65 + $janColumnIndex) . "): " . ($row[$janColumnIndex] ?? 'empty') . "\n";
        echo "  FEB (col " . chr(65 + $janColumnIndex + 1) . "): " . ($row[$janColumnIndex + 1] ?? 'empty') . "\n";
        echo "  MAR (col " . chr(65 + $janColumnIndex + 2) . "): " . ($row[$janColumnIndex + 2] ?? 'empty') . "\n";
    }
}
