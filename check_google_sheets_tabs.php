<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Google\Client;
use Google\Service\Sheets;

echo "=== Checking Google Sheets Tabs ===\n\n";

$client = new Client();
$client->setApplicationName('Dashboard PLN');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(storage_path('app/google/service-account.json'));

$service = new Sheets($client);
$spreadsheetId = env('GOOGLE_SPREADSHEET_ID');

try {
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheets = $spreadsheet->getSheets();
    
    echo "Total sheets: " . count($sheets) . "\n\n";
    
    echo "Looking for SEMUA/TARIF sheets:\n";
    echo str_repeat("=", 80) . "\n";
    
    $ulpSheets = [];
    foreach ($sheets as $sheet) {
        $title = $sheet->getProperties()->getTitle();
        if (strpos($title, 'SEMUA/TARIF') !== false) {
            echo "✓ {$title}\n";
            $ulpSheets[] = $title;
        }
    }
    
    echo str_repeat("=", 80) . "\n";
    echo "\nTotal SEMUA/TARIF sheets found: " . count($ulpSheets) . "\n";
    
    // Check expected sheets
    $expected = [
        'SEMUA/TARIF B.SEL',
        'SEMUA/TARIF B.UTARA',
        'SEMUA/TARIF SAMBOJA',
        'SEMUA/TARIF PETUNG',
        'SEMUA/TARIF LONGIKIS',
        'SEMUA/TARIF T.G.',
    ];
    
    echo "\nChecking expected sheets:\n";
    foreach ($expected as $exp) {
        $found = in_array($exp, $ulpSheets);
        echo ($found ? "✓" : "✗") . " {$exp}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
