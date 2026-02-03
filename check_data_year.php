<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STATISTIK DATA PER TAHUN ===\n\n";

echo "Data Pelanggan 2025:\n";
echo "- Total: " . DB::table('customer_data')->where('year', 2025)->count() . " records\n";
echo "- BULANAN: " . DB::table('customer_data')->where('year', 2025)->where('data_type', 'bulanan')->count() . " records\n";
echo "- KOMULATIF: " . DB::table('customer_data')->where('year', 2025)->where('data_type', 'kumulatif')->count() . " records\n\n";

echo "Data Daya 2025:\n";
echo "- Total: " . DB::table('power_data')->where('year', 2025)->count() . " records\n";
echo "- BULANAN: " . DB::table('power_data')->where('year', 2025)->where('data_type', 'bulanan')->count() . " records\n";
echo "- KOMULATIF: " . DB::table('power_data')->where('year', 2025)->where('data_type', 'kumulatif')->count() . " records\n\n";

echo "Data Pelanggan 2026:\n";
echo "- Total: " . DB::table('customer_data')->where('year', 2026)->count() . " records\n\n";

echo "Data Daya 2026:\n";
echo "- Total: " . DB::table('power_data')->where('year', 2026)->count() . " records\n\n";

echo "=== CONTOH DATA PELANGGAN BULANAN 2025 ===\n";
$sampleCustomer = DB::table('customer_data')
    ->where('year', 2025)
    ->where('data_type', 'bulanan')
    ->orderBy('ulp_code')
    ->orderBy('month')
    ->take(5)
    ->get();

foreach ($sampleCustomer as $row) {
    echo sprintf(
        "ULP: %s (%s) | %s 2025 | %s | Jumlah: %s\n",
        $row->ulp_code,
        $row->ulp_name,
        $row->month,
        strtoupper($row->data_type),
        number_format($row->customer_count, 0, ',', '.')
    );
}
