<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Testing Dashboard Query for PETUNG + JANUARI ===\n\n";

$year = 2025;
$month = 0; // Januari
$ulp = 'PETUNG';

echo "Parameters:\n";
echo "- Year: {$year}\n";
echo "- Month: {$month}\n";
echo "- ULP: {$ulp}\n\n";

// Query sama seperti di controller
$detailData = DB::table('tarif_customer_data')
    ->select(
        'tarif_code',
        'tarif_name',
        'tarif_category',
        DB::raw('MIN(row_order) as row_order'),
        DB::raw('SUM(total_customers) as customers')
    )
    ->where('year', $year)
    ->when($month !== null && $month !== '', function($q) use ($month) {
        return $q->where('month', $month);
    })
    ->when($ulp !== null && $ulp !== '', function($q) use ($ulp) {
        return $q->where('ulp_code', $ulp);
    })
    ->groupBy('tarif_code', 'tarif_name', 'tarif_category')
    ->orderBy('row_order')
    ->get();

echo "Results (first 15 records):\n";
echo str_repeat("=", 100) . "\n";
printf("%-8s %-50s %15s\n", "Category", "Tarif Name", "Customers");
echo str_repeat("=", 100) . "\n";

$count = 0;
foreach ($detailData as $tarif) {
    printf("%-8s %-50s %15s\n", $tarif->tarif_category, $tarif->tarif_name, number_format($tarif->customers));
    
    $count++;
    if ($count >= 15) {
        echo "... (showing first 15 of {$detailData->count()} total)\n";
        break;
    }
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "Total tarif records: " . $detailData->count() . "\n";

// Check kategori S specifically
echo "\nKategori S records:\n";
echo str_repeat("=", 80) . "\n";
$kategoriS = $detailData->filter(function($item) {
    return $item->tarif_category === 'S';
});

foreach ($kategoriS as $tarif) {
    echo "{$tarif->tarif_name} = {$tarif->customers}\n";
}
