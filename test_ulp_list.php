<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Testing ULP List Query ===\n\n";

// Query sama seperti di controller
$ulpList = DB::table('tarif_customer_data')
    ->select('ulp_code', 'ulp_name')
    ->whereNotNull('ulp_code')
    ->distinct()
    ->orderBy('ulp_code')
    ->get();

echo "Total ULP found: " . $ulpList->count() . "\n\n";

echo "ULP List:\n";
echo str_repeat("=", 60) . "\n";
printf("%-15s %-40s\n", "ULP Code", "ULP Name");
echo str_repeat("=", 60) . "\n";

foreach ($ulpList as $ulp) {
    printf("%-15s %-40s\n", $ulp->ulp_code, $ulp->ulp_name);
}

echo str_repeat("=", 60) . "\n";

// Check also if there's null ULP
$nullCount = DB::table('tarif_customer_data')
    ->whereNull('ulp_code')
    ->count();

echo "\nRecords with NULL ulp_code: {$nullCount}\n";

$notNullCount = DB::table('tarif_customer_data')
    ->whereNotNull('ulp_code')
    ->count();

echo "Records with NOT NULL ulp_code: {$notNullCount}\n";
