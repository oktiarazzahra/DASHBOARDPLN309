<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Google\Client;
use Google\Service\Sheets;

$client = new Client();
$client->setAuthConfig(storage_path('app/google/service-account.json'));
$client->addScope(Sheets::SPREADSHEETS_READONLY);
$service = new Sheets($client);
$spreadsheetId = '1vJicMHbG0gq380G4GA1wg9HYZmgYwKdxISKQfg0m7Ek';

echo "Checking PELANGGAN/TARIF sheet...\n\n";

try {
    $range = 'PELANGGAN/TARIF!A1:D10';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
    
    foreach ($values as $i => $row) {
        echo "Row " . ($i+1) . ": " . implode(' | ', $row) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
