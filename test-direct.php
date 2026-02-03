<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$serviceAccountPath = __DIR__ . '/storage/app/google/service-account.json';

echo "Testing Direct Google API Client...\n";
echo "Service Account Path: $serviceAccountPath\n";
echo "File exists: " . (file_exists($serviceAccountPath) ? 'YES' : 'NO') . "\n\n";

try {
    $client = new Client();
    $client->setApplicationName('Dashboard PLN 309');
    $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
    $client->setAuthConfig($serviceAccountPath);
    
    $service = new Sheets($client);
    $spreadsheetId = '1vJicMHbG0gq380G4GA1wg9HYZmgYwKdxISKQfg0m7Ek';
    $range = 'JUMLAH PELANGGAN PER ULP!A1:Z100';
    
    echo "Fetching data from: $range\n\n";
    
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
    
    if (empty($values)) {
        echo "No data found.\n";
    } else {
        echo "Success! Found " . count($values) . " rows\n\n";
        echo "First 5 rows:\n";
        foreach (array_slice($values, 0, 5) as $index => $row) {
            echo "Row $index: " . json_encode($row) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
