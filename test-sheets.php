<?php

require __DIR__.'/vendor/autoload.php';

use Revolution\Google\Sheets\Facades\Sheets;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing Google Sheets connection...\n";
    echo "Service Enabled: " . (config('google.service.enable') ? 'true' : 'false') . "\n";
    echo "Service Account File: " . config('google.service.file') . "\n";
    echo "File exists: " . (file_exists(storage_path('app/google/service-account.json')) ? 'YES' : 'NO') . "\n";
    echo "Spreadsheet ID: " . config('google.sheets.spreadsheet_id') . "\n";
    echo "Sheet Name: " . config('google.sheets.customer_sheet_name') . "\n\n";
    
    // Try to read service account directly
    $serviceAccountPath = storage_path('app/google/service-account.json');
    if (file_exists($serviceAccountPath)) {
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        echo "Service account email: " . ($serviceAccount['client_email'] ?? 'NOT FOUND') . "\n\n";
    }
    
    $data = Sheets::spreadsheet(config('google.sheets.spreadsheet_id'))
        ->sheet(config('google.sheets.customer_sheet_name'))
        ->get();
    
    echo "Total rows fetched: " . $data->count() . "\n\n";
    
    if ($data->count() > 0) {
        echo "First 5 rows:\n";
        foreach ($data->take(5) as $index => $row) {
            echo "Row $index: " . json_encode($row) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "\nFull trace:\n" . $e->getTraceAsString() . "\n";
}
