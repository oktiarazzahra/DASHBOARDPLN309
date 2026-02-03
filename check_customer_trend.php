<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerData;

echo "=== Data Pelanggan Per Bulan (BULANAN) ===\n\n";

$ulps = CustomerData::where('year', 2025)
    ->where('data_type', 'bulanan')
    ->select('ulp_code', 'ulp_name')
    ->distinct()
    ->orderBy('ulp_code')
    ->get();

$months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

foreach ($ulps as $ulp) {
    echo "\n{$ulp->ulp_code} - {$ulp->ulp_name}:\n";
    
    $prevCount = 0;
    foreach ($months as $month) {
        $data = CustomerData::where('year', 2025)
            ->where('data_type', 'bulanan')
            ->where('ulp_code', $ulp->ulp_code)
            ->where('month', $month)
            ->first();
        
        if ($data) {
            $trend = '';
            if ($prevCount > 0) {
                if ($data->customer_count > $prevCount) {
                    $trend = " ↑";
                } elseif ($data->customer_count < $prevCount) {
                    $trend = " ↓ TURUN!";
                } else {
                    $trend = " →";
                }
            }
            echo "  {$month}: " . number_format($data->customer_count, 0, ',', '.') . $trend . "\n";
            $prevCount = $data->customer_count;
        } else {
            echo "  {$month}: NO DATA\n";
        }
    }
}
