<?php

namespace App\Services;

use App\Models\RevenueData;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class RevenueSheetsService
{
    protected $spreadsheetId;
    protected $sheetName;
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->spreadsheetId = config('google.sheets.spreadsheet_id');
        $this->sheetName = 'RUPIAH KWH PER ULP'; // Sheet 4
        
        // Initialize Google Client with Service Account
        $this->client = new Client();
        $this->client->setApplicationName('Dashboard PLN 309');
        $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('app/google/service-account.json'));
        
        // DISABLE CACHE - Force fresh data dari Google Sheets setiap kali sync
        $httpClient = new \GuzzleHttp\Client([
            'headers' => [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]
        ]);
        $this->client->setHttpClient($httpClient);
        
        $this->service = new Sheets($this->client);
    }

    /**
     * Ambil data dari Google Sheets
     */
    public function fetchData()
    {
        try {
            // Expand range to AT (column 45) to ensure Google Sheets API returns all columns
            // Even if some columns at the end are empty
            $range = $this->sheetName . '!A1:AT1000';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return collect();
            }
            
            return collect($values);
        } catch (\Exception $e) {
            Log::error('Error fetching Revenue Sheets data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Sync data revenue dari Google Sheets ke database
     * Support multi-tahun (2025, 2026, dst)
     */
    public function syncToDatabase()
    {
        try {
            $data = $this->fetchData();
            $syncedCount = 0;

            if ($data->isEmpty()) {
                Log::warning('No revenue data found in Google Sheets');
                return 0;
            }

            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            
            // Cari semua section BULANAN (untuk multi-tahun)
            $bulaanSections = [];
            foreach ($data as $index => $row) {
                if (isset($row[0]) && strtoupper(trim($row[0])) === 'BULANAN') {
                    // Cari tahun dari row sebelumnya (biasanya di title)
                    $year = null;
                    for ($j = max(0, $index - 5); $j < $index; $j++) {
                        if (isset($data[$j][0]) && preg_match('/(\d{4})/', $data[$j][0], $matches)) {
                            $year = (int)$matches[1];
                            break;
                        }
                    }
                    if (!$year) $year = 2025; // Default
                    
                    $bulaanSections[] = [
                        'year' => $year,
                        'start_index' => $index
                    ];
                }
            }

            if (empty($bulaanSections)) {
                Log::warning('Could not find any BULANAN section in revenue data');
                return 0;
            }

            // Process setiap section BULANAN (2025, 2026, dst)
            foreach ($bulaanSections as $sectionIndex => $section) {
                $year = $section['year'];
                $headerRowIndex = $section['start_index'];
                $dataType = 'bulanan';
                
                // Tentukan batas akhir section (sampai KOMULATIF atau section berikutnya)
                $endIndex = count($data);
                for ($j = $headerRowIndex + 1; $j < count($data); $j++) {
                    if (isset($data[$j][0]) && strtoupper(trim($data[$j][0])) === 'KOMULATIF') {
                        $endIndex = $j;
                        break;
                    }
                }
                
                Log::info("Processing BULANAN section for year {$year}, rows " . ($headerRowIndex + 2) . " to {$endIndex}");
                
                // Process data ULP dalam section ini
                for ($i = $headerRowIndex + 2; $i < $endIndex; $i++) {
                    $row = $data[$i];
                    
                    // Stop if reached KOMULATIF section
                    if (isset($row[0]) && strtoupper($row[0]) === 'KOMULATIF') {
                        break;
                    }

                    // Skip empty rows
                    if (empty($row[0]) || trim($row[0]) === '') {
                        continue;
                    }

                    // Skip UP3 BALIKPAPAN total row
                    if (isset($row[0]) && strtoupper(trim($row[0])) === 'UP3 BALIKPAPAN') {
                        continue;
                    }

                    $ulpCode = isset($row[0]) ? trim($row[0]) : null;
                    $ulpName = isset($row[1]) ? trim($row[1]) : null;

                    // Skip jika tidak ada kode ULP atau nama ULP
                    if (!$ulpCode || !$ulpName) {
                        continue;
                    }

                    // Skip jika kode ULP bukan angka
                    if (!is_numeric($ulpCode)) {
                        continue;
                    }

                    // Process each month
                    // kWh Jual: columns C-N (index 2-13) for JAN-DEC
                    // Rp Pendapatan: columns S-AD (index 18-29) for JAN-DEC  
                    // Rp/kWh: columns AI-AT (index 34-45) for JAN-DEC
                    for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
                        // kWh Jual column
                        $kwhColumn = $monthIndex + 2;
                        $kwhJual = isset($row[$kwhColumn]) ? 
                            (int)str_replace([',', '.'], '', trim($row[$kwhColumn])) : 0;
                        
                        // Rp Pendapatan column (kolom S = index 18, JAN)
                        $rpColumn = $monthIndex + 18;
                        $rpPendapatan = isset($row[$rpColumn]) ? 
                            (int)str_replace([',', '.'], '', trim($row[$rpColumn])) : 0;
                        
                        // Rp/kWh column (setelah Rp Pendapatan)
                        $rpPerKwhColumn = $monthIndex + 34;
                        $rpPerKwhRaw = isset($row[$rpPerKwhColumn]) ? trim($row[$rpPerKwhColumn]) : '0';
                        // Rp/kWh uses comma as decimal separator (e.g., "1,076" = 1.076)
                        $rpPerKwh = (float)str_replace(',', '.', str_replace('.', '', $rpPerKwhRaw));

                        // Insert even if 0 (for 2026 empty columns)
                        RevenueData::updateOrCreate(
                            [
                                'ulp_code' => $ulpCode,
                                'month' => $months[$monthIndex],
                                'year' => $year,
                                'data_type' => $dataType,
                            ],
                            [
                                'ulp_name' => $ulpName,
                                'kwh_jual' => $kwhJual,
                                'rp_pendapatan' => $rpPendapatan,
                                'rp_per_kwh' => $rpPerKwh,
                            ]
                        );
                        $syncedCount++;
                    }
                }
            }

            Log::info("Synced {$syncedCount} revenue records from Google Sheets");
            return $syncedCount;

        } catch (\Exception $e) {
            Log::error('Error syncing Revenue Sheets data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Ambil statistik revenue
     */
    public function getStatistics($year = null)
    {
        $year = $year ?? 2025;
        
        return [
            'total_ulp' => RevenueData::byYear($year)->distinct('ulp_code')->count('ulp_code'),
            'total_kwh_bulanan' => RevenueData::byYear($year)->bulanan()->sum('kwh_jual'),
            'total_rp_bulanan' => RevenueData::byYear($year)->bulanan()->sum('rp_pendapatan'),
            'latest_month' => RevenueData::byYear($year)->orderBy('id', 'desc')->first()?->month,
            'year' => $year,
        ];
    }

    /**
     * Ambil data per ULP
     */
    public function getDataByUlp($ulpCode, $year = null)
    {
        $year = $year ?? 2025;
        
        $data = RevenueData::byUlp($ulpCode)
            ->byYear($year)
            ->get();
        
        // Manual sort untuk SQLite compatibility
        $monthOrder = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        return $data->sortBy(function($item) use ($monthOrder) {
            return array_search($item->month, $monthOrder);
        })->values();
    }

    /**
     * Ambil semua ULP
     */
    public function getAllUlps($year = null)
    {
        $year = $year ?? 2025;
        
        return RevenueData::byYear($year)
            ->select('ulp_code', 'ulp_name')
            ->distinct()
            ->orderBy('ulp_code')
            ->get();
    }

    /**
     * Ambil data chart bulanan
     */
    public function getMonthlyChartData($year = null)
    {
        $year = $year ?? 2025;
        
        $data = RevenueData::byYear($year)
            ->bulanan()
            ->selectRaw('month, SUM(kwh_jual) as total_kwh, SUM(rp_pendapatan) as total_rp')
            ->groupBy('month')
            ->get();
        
        // Manual sort untuk SQLite compatibility
        $monthOrder = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        return $data->sortBy(function($item) use ($monthOrder) {
            return array_search($item->month, $monthOrder);
        })->values();
    }
    
    /**
     * Ambil data chart per ULP
     */
    public function getChartDataByUlp($year = null)
    {
        $year = $year ?? 2025;
        $monthOrder = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        
        $ulps = $this->getAllUlps($year);
        $result = [];
        
        foreach ($ulps as $ulp) {
            $data = RevenueData::byYear($year)
                ->byUlp($ulp->ulp_code)
                ->bulanan()
                ->get()
                ->sortBy(function($item) use ($monthOrder) {
                    return array_search($item->month, $monthOrder);
                });
            
            $result[] = [
                'ulp_code' => $ulp->ulp_code,
                'ulp_name' => $ulp->ulp_name,
                'kwh_data' => $data->pluck('kwh_jual')->toArray(),
                'rp_data' => $data->pluck('rp_pendapatan')->toArray(),
                'months' => $data->pluck('month')->toArray()
            ];
        }
        
        return $result;
    }
}
