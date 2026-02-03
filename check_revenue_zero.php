<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RevenueData;

echo "=== CHECKING REVENUE DATA WITH ZERO VALUES ===\n\n";

// Cek data Rp Pendapatan yang 0
$zeroRevenue = RevenueData::where('rp_pendapatan', 0)
    ->where('year', 2025)
    ->orderBy('ulp_code')
    ->orderBy('month')
    ->get();

echo "Total records with Rp Pendapatan = 0: " . $zeroRevenue->count() . "\n\n";

if ($zeroRevenue->count() > 0) {
    echo "Details:\n";
    foreach ($zeroRevenue as $record) {
        echo sprintf(
            "ULP: %s (%s) - Month: %s - kWh: %s - Rp: %s - Rp/kWh: %s\n",
            $record->ulp_code,
            $record->ulp_name,
            $record->month,
            number_format($record->kwh_jual),
            number_format($record->rp_pendapatan),
            $record->rp_per_kwh
        );
    }
}

echo "\n=== SAMPLE DATA PER ULP ===\n\n";

// Ambil sample data per ULP untuk lihat polanya
$ulps = RevenueData::where('year', 2025)
    ->groupBy('ulp_code')
    ->pluck('ulp_code');

foreach ($ulps as $ulpCode) {
    echo "\nULP: $ulpCode\n";
    echo str_repeat("-", 80) . "\n";
    
    $records = RevenueData::where('ulp_code', $ulpCode)
        ->where('year', 2025)
        ->orderBy('month')
        ->get();
    
    foreach ($records as $record) {
        echo sprintf(
            "%s: kWh=%15s | Rp=%15s | Rp/kWh=%s\n",
            str_pad($record->month, 4),
            number_format($record->kwh_jual),
            number_format($record->rp_pendapatan),
            number_format($record->rp_per_kwh, 3)
        );
    }
}
