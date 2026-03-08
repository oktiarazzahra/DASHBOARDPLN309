<?php

namespace App\Http\Controllers;

use App\Models\CustomerData;
use App\Models\PowerData;
use App\Models\RevenueData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    /**
     * Halaman debug untuk melihat data di database
     * Akses: yourdomain.com/debug-data
     */
    public function index(Request $request)
    {
        $year = (int)$request->get('year', 2026);
        
        // Hitung jumlah record per tabel per tahun
        $customerCount = CustomerData::where('year', $year)->count();
        $powerCount = PowerData::where('year', $year)->count();
        $revenueCount = RevenueData::where('year', $year)->count();
        
        // Total semua tahun
        $customerTotal = CustomerData::count();
        $powerTotal = PowerData::count();
        $revenueTotal = RevenueData::count();
        
        // Ambil tahun yang ada di database
        $availableYears = CustomerData::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        // Ambil 10 record terbaru untuk preview
        $recentCustomers = CustomerData::where('year', $year)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        $recentPower = PowerData::where('year', $year)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        $recentRevenue = RevenueData::where('year', $year)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        // Data per ULP
        $customerByUlp = CustomerData::select('ulp_code', 'ulp_name', DB::raw('COUNT(*) as count'))
            ->where('year', $year)
            ->groupBy('ulp_code', 'ulp_name')
            ->orderBy('ulp_code')
            ->get();
        
        // Cek environment variables
        $envCheck = [
            'GOOGLE_SPREADSHEET_ID' => env('GOOGLE_SPREADSHEET_ID') ? '✅ Set' : '❌ Not Set',
            'GOOGLE_SERVICE_ACCOUNT_BASE64' => env('GOOGLE_SERVICE_ACCOUNT_BASE64') ? '✅ Set' : '❌ Not Set',
            'DB_CONNECTION' => env('DB_CONNECTION', 'sqlite'),
            'DB_DATABASE' => env('DB_DATABASE'),
        ];
        
        // Cek file database
        $dbPath = database_path('database.sqlite');
        $dbExists = file_exists($dbPath);
        $dbSize = $dbExists ? filesize($dbPath) : 0;
        $dbReadable = $dbExists && is_readable($dbPath);
        $dbWritable = $dbExists && is_writable($dbPath);
        
        return view('debug.index', compact(
            'year',
            'availableYears',
            'customerCount',
            'powerCount',
            'revenueCount',
            'customerTotal',
            'powerTotal',
            'revenueTotal',
            'recentCustomers',
            'recentPower',
            'recentRevenue',
            'customerByUlp',
            'envCheck',
            'dbExists',
            'dbSize',
            'dbReadable',
            'dbWritable',
            'dbPath'
        ));
    }
}
