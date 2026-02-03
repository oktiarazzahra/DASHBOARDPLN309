<?php

namespace App\Http\Controllers;

use App\Services\CustomerSheetsService;
use App\Services\PowerSheetsService;
use App\Services\RevenueSheetsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $customerService;
    protected $powerService;
    protected $revenueService;

    public function __construct(
        CustomerSheetsService $customerService, 
        PowerSheetsService $powerService,
        RevenueSheetsService $revenueService
    ) {
        $this->customerService = $customerService;
        $this->powerService = $powerService;
        $this->revenueService = $revenueService;
    }

    /**
     * Tampilkan dashboard
     */
    public function index(Request $request)
    {
        // Hanya support 2025 untuk saat ini (2026 belum ada data)
        $year = $request->get('year', 2025);
        
        // Validasi: jika tahun > 2025, redirect ke 2025
        if ($year > 2025) {
            return redirect('/?year=2025')->with('warning', 'Data tahun ' . $year . ' belum tersedia. Menampilkan data tahun 2025.');
        }
        
        $availableYears = [2025]; // Hanya 2025 yang tersedia

        // Statistik umum
        $customerStats = $this->customerService->getStatistics($year);
        $powerStats = $this->powerService->getStatistics($year);
        $revenueStats = $this->revenueService->getStatistics($year);

        // Data chart kumulatif (total yang selalu naik)
        $customerChartData = $this->customerService->getMonthlyChartData($year);
        $customerMonths = $customerChartData->pluck('month')->toArray();
        $customerTotals = $customerChartData->pluck('total')->toArray();

        $powerChartData = $this->powerService->getMonthlyChartData($year);
        $powerMonths = $powerChartData->pluck('month')->toArray();
        $powerTotals = $powerChartData->pluck('total')->toArray();

        // Data per ULP untuk chart
        $customerByUlp = $this->customerService->getChartDataByUlp($year);
        $powerByUlp = $this->powerService->getChartDataByUlp($year);
        $revenueByUlp = $this->revenueService->getChartDataByUlp($year);

        // Data per ULP untuk table
        $ulps = $this->customerService->getAllUlps($year);

        return view('dashboard.index', compact(
            'year',
            'availableYears',
            'customerStats',
            'powerStats',
            'revenueStats',
            'customerMonths',
            'customerTotals',
            'powerMonths',
            'powerTotals',
            'customerByUlp',
            'powerByUlp',
            'revenueByUlp',
            'ulps'
        ));
    }

    /**
     * Sync data dari Google Sheets
     */
    public function syncData()
    {
        try {
            $syncedCount = $this->googleSheetsService->syncToDatabase();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil sync {$syncedCount} data dari Google Sheets",
                'synced_count' => $syncedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil data monitoring untuk API
     */
    public function getData(Request $request)
    {
        $location = $request->get('location');
        $status = $request->get('status');

        if ($location) {
            $data = $this->googleSheetsService->getDataByLocation($location);
        } elseif ($status) {
            $data = $this->googleSheetsService->getDataByStatus($status);
        } else {
            $data = $this->googleSheetsService->getRecentData(50);
        }

        return response()->json($data);
    }

    /**
     * Ambil statistik untuk dashboard
     */
    public function getStatistics()
    {
        $statistics = $this->googleSheetsService->getStatistics();
        return response()->json($statistics);
    }
}
