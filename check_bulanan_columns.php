<?php
require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$client = new Client();
$client->setApplicationName('Dashboard PLN 309');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(__DIR__.'/storage/app/google/service-account.json');

$service = new Sheets($client);
$spreadsheetId = '1vJicMHbG0gq380G4GA1wg9HYZmgYwKdxISKQfg0m7Ek';
$range = 'RUPIAH/KWH!A1:AZ100';

$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

echo "\n=== CHECKING BULANAN SECTION ===\n";

// Find BULANAN row
$bulananRow = null;
foreach ($values as $index => $row) {
    if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
        $bulananRow = $index;
        echo "Found BULANAN at row " . ($index + 1) . " (index $index)\n\n";
        break;
    }
}

if ($bulananRow !== null) {
    // Show next 3 rows after BULANAN
    for ($i = $bulananRow; $i < $bulananRow + 4; $i++) {
        if (isset($values[$i])) {
            echo "Row " . ($i + 1) . " (index $i):\n";
            $row = $values[$i];
            
            // Show first 35 columns dengan label
            $columnLabels = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ'];
            
            for ($col = 0; $col < min(35, count($row)); $col++) {
                $value = isset($row[$col]) ? $row[$col] : '';
                $label = $columnLabels[$col] ?? "Col$col";
                echo "  $label (index $col): " . substr($value, 0, 20) . "\n";
            }
            echo "\n";
        }
    }
    
    // Show first data row (should be 23200)
    echo "\n=== FIRST ULP DATA ROW ===\n";
    $firstDataRow = $bulananRow + 3; // BULANAN + header + UP3 + first ULP
    if (isset($values[$firstDataRow])) {
        echo "Row " . ($firstDataRow + 1) . " (index $firstDataRow):\n";
        $row = $values[$firstDataRow];
        
        echo "ULP Code (col A): " . ($row[0] ?? 'N/A') . "\n";
        echo "ULP Name (col B): " . ($row[1] ?? 'N/A') . "\n\n";
        
        echo "kWh Jual samples:\n";
        echo "  JAN (col C, idx 2): " . ($row[2] ?? 'N/A') . "\n";
        echo "  DEC (col N, idx 13): " . ($row[13] ?? 'N/A') . "\n\n";
        
        echo "Rp Pendapatan samples:\n";
        echo "  Checking different column positions:\n";
        echo "  Col S (idx 18): " . ($row[18] ?? 'N/A') . "\n";
        echo "  Col T (idx 19): " . ($row[19] ?? 'N/A') . "\n";
        echo "  Col U (idx 20): " . ($row[20] ?? 'N/A') . "\n";
        echo "  Col AE (idx 30): " . ($row[30] ?? 'N/A') . "\n\n";
        
        echo "Rp/kWh samples:\n";
        echo "  Col AI (idx 34): " . ($row[34] ?? 'N/A') . "\n";
        echo "  Col AT (idx 45): " . ($row[45] ?? 'N/A') . "\n";
    }
}
