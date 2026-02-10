<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking ULP Data Details ===\n\n";

$ulps = ['B.SEL', 'B.UTARA', 'SAMBOJA', 'PETUNG', 'LONGIKIS', 'T.G.'];

foreach ($ulps as $ulp) {
    $count = DB::table('tarif_customer_data')
        ->where('ulp_code', $ulp)
        ->count();
    
    echo "{$ulp}: {$count} records\n";
}
