<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RevenueData;

$countK = RevenueData::where('data_type', 'kumulatif')->count();
$countB = RevenueData::where('data_type', 'bulanan')->count();
$types = RevenueData::select('data_type')->distinct()->pluck('data_type');

echo "Bulanan records: $countB\n";
echo "Kumulatif records: $countK\n";
echo "Data types: " . json_encode($types) . "\n\n";

if ($countK > 0) {
    $samples = RevenueData::where('data_type', 'kumulatif')->limit(3)->get();
    foreach ($samples as $s) {
        echo "ULP: {$s->ulp_code} | Month: {$s->month} | kWh: {$s->kwh_jual} | Rp: {$s->rp_pendapatan}\n";
    }
} else {
    echo "NO KUMULATIF DATA FOUND!\n";
    echo "\nChecking all data_type values:\n";
    $all = RevenueData::selectRaw('data_type, count(*) as cnt')->groupBy('data_type')->get();
    foreach ($all as $row) {
        echo "  {$row->data_type}: {$row->cnt} records\n";
    }
}
