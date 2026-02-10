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

echo "=== Comparing Sheet PETUNG vs Dashboard ===\n\n";

// Ambil data PELANGGAN section dari PETUNG
$range = 'SEMUA/TARIF PETUNG!A1:M20';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

echo "PELANGGAN Data from Google Sheets (PETUNG):\n";
echo str_repeat("=", 80) . "\n";

// Show header
echo "Row 1: " . implode(' | ', $values[0]) . "\n";
echo "Row 2: " . implode(' | ', $values[1]) . "\n";
echo "\n";

echo "Tarif Data (starting from Row 3):\n";
for ($i = 2; $i < min(20, count($values)); $i++) {
    if (!empty($values[$i][0])) {
        $tarifName = $values[$i][0];
        $jan = $values[$i][1] ?? '0';
        
        // Skip continuation dan subtotal rows
        if (in_array($tarifName, ['II', 'III']) || strpos($tarifName, 'JUMLAH') !== false) {
            echo "  [SKIP] Row " . ($i + 1) . ": {$tarifName}\n";
            continue;
        }
        
        echo "  Row " . ($i + 1) . ": {$tarifName} = JAN: {$jan}\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Check database
echo "Data in Database (ULP: PETUNG, Month: 0/JAN):\n";
echo str_repeat("=", 80) . "\n";

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$dbData = DB::table('tarif_customer_data')
    ->where('ulp_code', 'PETUNG')
    ->where('year', 2025)
    ->where('month', 0)
    ->orderBy('tarif_name')
    ->get();

foreach ($dbData as $row) {
    echo "  {$row->tarif_name} = {$row->total_customers}\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Total records in DB for PETUNG: " . $dbData->count() . "\n";
