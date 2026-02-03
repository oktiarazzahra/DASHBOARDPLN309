<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PowerData;

echo "=== Data Daya Tersambung Per Bulan (BULANAN) ===\n\n";

$ulps = PowerData::where('year', 2025)
    ->where('data_type', 'bulanan')
    ->select('ulp_code', 'ulp_name')
    ->distinct()
    ->orderBy('ulp_code')
    ->get();

$months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

foreach ($ulps as $ulp) {
    echo "\n{$ulp->ulp_code} - {$ulp->ulp_name}:\n";
    
    foreach ($months as $month) {
        $data = PowerData::where('year', 2025)
            ->where('data_type', 'bulanan')
            ->where('ulp_code', $ulp->ulp_code)
            ->where('month', $month)
            ->first();
        
        if ($data) {
            echo "  {$month}: " . number_format($data->power_va, 0, ',', '.') . " VA\n";
        } else {
            echo "  {$month}: NO DATA\n";
        }
    }
}

echo "\n=== Total Per Bulan (Semua ULP) ===\n";
foreach ($months as $month) {
    $total = PowerData::where('year', 2025)
        ->where('data_type', 'bulanan')
        ->where('month', $month)
        ->sum('power_va');
    
    echo "{$month}: " . number_format($total, 0, ',', '.') . " VA\n";
}
