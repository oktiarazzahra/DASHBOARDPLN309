<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking All Tarif Data ===\n\n";

$totalCustomer = DB::table('tarif_customer_data')->count();
$totalPower = DB::table('tarif_power_data')->count();
$totalRevenue = DB::table('tarif_revenue_data')->count();

echo "Total records:\n";
echo "- tarif_customer_data: {$totalCustomer}\n";
echo "- tarif_power_data: {$totalPower}\n";
echo "- tarif_revenue_data: {$totalRevenue}\n\n";

// Check ULP data
$ulpCustomer = DB::table('tarif_customer_data')->whereNotNull('ulp_code')->count();
$ulpPower = DB::table('tarif_power_data')->whereNotNull('ulp_code')->count();
$ulpRevenue = DB::table('tarif_revenue_data')->whereNotNull('ulp_code')->count();

echo "Records with ULP:\n";
echo "- tarif_customer_data: {$ulpCustomer}\n";
echo "- tarif_power_data: {$ulpPower}\n";
echo "- tarif_revenue_data: {$ulpRevenue}\n\n";

// Check NULL ULP
$nullCustomer = DB::table('tarif_customer_data')->whereNull('ulp_code')->count();
$nullPower = DB::table('tarif_power_data')->whereNull('ulp_code')->count();
$nullRevenue = DB::table('tarif_revenue_data')->whereNull('ulp_code')->count();

echo "Records with NULL ULP (Semua ULP):\n";
echo "- tarif_customer_data: {$nullCustomer}\n";
echo "- tarif_power_data: {$nullPower}\n";
echo "- tarif_revenue_data: {$nullRevenue}\n\n";

// Sample data
echo "Sample customer data (first 5):\n";
$samples = DB::table('tarif_customer_data')
    ->select('tarif_code', 'tarif_name', 'ulp_code', 'ulp_name', 'year', 'month')
    ->limit(5)
    ->get();

foreach ($samples as $s) {
    echo "  {$s->tarif_code} | ULP: " . ($s->ulp_code ?? 'NULL') . "\n";
}
