<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$data = App\Models\RevenueData::where('year', 2025)
    ->whereIn('month', ['SEP','OCT','NOV','DEC'])
    ->where('data_type', 'bulanan')
    ->orderBy('month')
    ->orderBy('ulp_code')
    ->get(['ulp_code','ulp_name','month','rp_pendapatan']);

echo "\n=== CHECKING SEP-DEC Rp Pendapatan ===\n\n";
foreach($data as $d) {
    $rp = number_format($d->rp_pendapatan, 0, ',', '.');
    echo $d->ulp_code . ' ' . str_pad($d->ulp_name, 20) . ' - ' . $d->month . ': Rp ' . $rp . "\n";
}

echo "\n";
