<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking 2026 Data ===\n\n";

// Check revenue data
$revenue2026 = DB::table('revenue_data')->where('year', 2026)->count();
echo "Revenue data 2026: {$revenue2026} records\n";

if ($revenue2026 > 0) {
    echo "\nSample revenue 2026:\n";
    DB::table('revenue_data')
        ->where('year', 2026)
        ->limit(5)
        ->get()
        ->each(function($row) {
            echo "  - {$row->ulp_name} | {$row->month} | {$row->data_type} | " . number_format($row->value) . "\n";
        });
}

// Check customer data
$customer2026 = DB::table('customer_data')->where('year', 2026)->count();
echo "\nCustomer data 2026: {$customer2026} records\n";

// Check power data
$power2026 = DB::table('power_data')->where('year', 2026)->count();
echo "Power data 2026: {$power2026} records\n";

echo "\n=== Summary ===\n";
echo "Total 2026 records: " . ($revenue2026 + $customer2026 + $power2026) . "\n";

if ($revenue2026 + $customer2026 + $power2026 > 0) {
    echo "\n⚠️  WARNING: Tahun 2026 seharusnya belum ada data!\n";
    echo "Kemungkinan data ter-sync dari sheet yang salah atau default value.\n";
} else {
    echo "\n✅ OK: Tidak ada data 2026 (sesuai harapan)\n";
}
