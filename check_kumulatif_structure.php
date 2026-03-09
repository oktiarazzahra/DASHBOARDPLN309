<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\RevenueSheetsService;

$service = new RevenueSheetsService();
$data = $service->fetchData();

echo "Total rows: " . $data->count() . "\n\n";

// Find BULANAN and KOMULATIF markers
foreach ($data as $index => $row) {
    $firstCol = isset($row[0]) ? strtoupper(trim($row[0])) : '';
    if (in_array($firstCol, ['BULANAN', 'KOMULATIF', 'KUMULATIF'])) {
        echo "Row $index: [{$firstCol}]\n";
        // Show next 3 rows for context
        for ($j = $index + 1; $j <= min($index + 3, $data->count() - 1); $j++) {
            $cols = array_slice($data[$j] ?? [], 0, 5);
            echo "  Row $j: " . json_encode($cols) . "\n";
        }
        echo "\n";
    }
    // Also check for year markers
    if (isset($row[0]) && preg_match('/\d{4}/', $row[0])) {
        echo "Row $index (year?): [{$row[0]}]\n";
    }
}
