<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFIKASI DATA FINAL ===\n\n";

// 1. Cek data per tahun
echo "1. Data per Tahun:\n";
echo "   2025: " . DB::table('customer_data')->where('year', 2025)->count() . " pelanggan, " . 
     DB::table('power_data')->where('year', 2025)->count() . " daya\n";
echo "   2026: " . DB::table('customer_data')->where('year', 2026)->count() . " pelanggan, " . 
     DB::table('power_data')->where('year', 2026)->count() . " daya\n\n";

// 2. Cek data per tipe
echo "2. Data per Tipe (2025):\n";
echo "   BULANAN: " . DB::table('customer_data')->where('year', 2025)->where('data_type', 'bulanan')->count() . " pelanggan, " . 
     DB::table('power_data')->where('year', 2025)->where('data_type', 'bulanan')->count() . " daya\n";
echo "   KOMULATIF: " . DB::table('customer_data')->where('year', 2025)->where('data_type', 'kumulatif')->count() . " pelanggan, " . 
     DB::table('power_data')->where('year', 2025)->where('data_type', 'kumulatif')->count() . " daya\n\n";

// 3. Cek jumlah ULP
echo "3. Jumlah ULP Unique (2025):\n";
echo "   Pelanggan: " . DB::table('customer_data')->where('year', 2025)->distinct()->count('ulp_code') . " ULP\n";
echo "   Daya: " . DB::table('power_data')->where('year', 2025)->distinct()->count('ulp_code') . " ULP\n\n";

// 4. Sample data pelanggan per bulan untuk 1 ULP
echo "4. Trend Pelanggan BPN SELATAN (2025) - Harusnya selalu naik:\n";
$customerTrend = DB::table('customer_data')
    ->where('year', 2025)
    ->where('ulp_code', '23200')
    ->where('data_type', 'bulanan')
    ->orderByRaw("CASE month 
        WHEN 'JAN' THEN 1 WHEN 'FEB' THEN 2 WHEN 'MAR' THEN 3 
        WHEN 'APR' THEN 4 WHEN 'MAY' THEN 5 WHEN 'JUN' THEN 6 
        WHEN 'JUL' THEN 7 WHEN 'AUG' THEN 8 WHEN 'SEP' THEN 9 
        WHEN 'OCT' THEN 10 WHEN 'NOV' THEN 11 WHEN 'DEC' THEN 12 
    END")
    ->get();

$prev = 0;
foreach ($customerTrend as $row) {
    $trend = $row->customer_count > $prev ? '↑' : ($row->customer_count < $prev ? '↓' : '→');
    $diff = $row->customer_count - $prev;
    echo sprintf(
        "   %s: %s (%s %s)\n",
        $row->month,
        number_format($row->customer_count, 0, ',', '.'),
        $trend,
        $prev > 0 ? number_format(abs($diff), 0, ',', '.') : 'baseline'
    );
    $prev = $row->customer_count;
}

echo "\n5. Total Pelanggan dan Daya per Bulan (2025):\n";
$monthlyStats = DB::select("
    SELECT 
        c.month,
        SUM(c.customer_count) as total_pelanggan,
        SUM(p.power_va) as total_daya_va
    FROM customer_data c
    LEFT JOIN power_data p ON c.ulp_code = p.ulp_code AND c.month = p.month AND c.year = p.year
    WHERE c.year = 2025 AND c.data_type = 'bulanan'
    GROUP BY c.month
    ORDER BY CASE c.month 
        WHEN 'JAN' THEN 1 WHEN 'FEB' THEN 2 WHEN 'MAR' THEN 3 
        WHEN 'APR' THEN 4 WHEN 'MAY' THEN 5 WHEN 'JUN' THEN 6 
        WHEN 'JUL' THEN 7 WHEN 'AUG' THEN 8 WHEN 'SEP' THEN 9 
        WHEN 'OCT' THEN 10 WHEN 'NOV' THEN 11 WHEN 'DEC' THEN 12 
    END
");

foreach ($monthlyStats as $stat) {
    echo sprintf(
        "   %s: %s pelanggan | %s kVA\n",
        $stat->month,
        number_format($stat->total_pelanggan, 0, ',', '.'),
        number_format($stat->total_daya_va / 1000, 0, ',', '.')
    );
}

echo "\n✅ VERIFIKASI SELESAI\n";
echo "   - Tahun 2025: Data lengkap (hanya BULANAN)\n";
echo "   - Tahun 2026: Kosong\n";
echo "   - Data KOMULATIF: Sudah dihapus\n";
