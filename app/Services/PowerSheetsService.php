<?php

namespace App\Services;

use App\Models\PowerData;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class PowerSheetsService
{
    protected $spreadsheetId;
    protected $sheetName;
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->spreadsheetId = config('google.sheets.spreadsheet_id');
        $this->sheetName = 'JUMLAH DAYA PER ULP';
        
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
            $range = $this->sheetName . '!A1:Z1000';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return collect();
            }
            
            return collect($values);
        } catch (\Exception $e) {
            Log::error('Error fetching Power Sheets data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Sync data daya dari Google Sheets ke database
     * Support multi-tahun (2025, 2026, dst)
     */
    public function syncToDatabase()
    {
        try {
            $data = $this->fetchData();
            $syncedCount = 0;

            if ($data->isEmpty()) {
                Log::warning('No power data found in Google Sheets');
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
                Log::warning('Could not find any BULANAN section in power data');
                return 0;
            }

            // Process setiap section BULANAN (2025, 2026, dst)
            foreach ($bulaanSections as $sectionIndex => $section) {
                $year = $section['year'];
                $headerRowIndex = $section['start_index'];
                $dataType = 'bulanan';
                
                // Tentukan batas akhir section (sampai KOMULATIF atau section BULANAN berikutnya)
                $endIndex = count($data);
                // Stop at next BULANAN section to prevent overwriting current section's data with 0
                if (isset($bulaanSections[$sectionIndex + 1])) {
                    $endIndex = $bulaanSections[$sectionIndex + 1]['start_index'];
                }
                // Also stop at KOMULATIF if it comes before next BULANAN
                for ($j = $headerRowIndex + 1; $j < $endIndex; $j++) {
                    if (isset($data[$j][0]) && strtoupper(trim($data[$j][0])) === 'KOMULATIF') {
                        $endIndex = $j;
                        break;
                    }
                }
                
                Log::info("Processing BULANAN power section for year {$year}, rows " . ($headerRowIndex + 2) . " to {$endIndex}");
                
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

                    // Cek apakah row ini adalah "UP3 BALIKPAPAN" (total row) - double check
                    if (isset($row[0]) && strtoupper(trim($row[0])) === 'UP3 BALIKPAPAN') {
                        continue;
                    }

                    $ulpCode = isset($row[0]) ? trim($row[0]) : null;
                    $ulpName = isset($row[1]) ? trim($row[1]) : null;

                    // Skip jika tidak ada kode ULP atau nama ULP
                    if (!$ulpCode || !$ulpName) {
                        continue;
                    }

                    // Skip jika kode ULP bukan angka (untuk memastikan hanya data ULP yang diproses)
                    if (!is_numeric($ulpCode)) {
                        continue;
                    }

                    // Process each month (columns 2-13 for JAN-DEC)
                    // Data starts from column C (index 2)
                    for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
                        $columnIndex = $monthIndex + 2; // Column C=2, D=3, E=4, etc.
                        $powerVa = isset($row[$columnIndex]) ? 
                            (int)str_replace([',', '.'], '', trim($row[$columnIndex])) : 0;

                        // Insert even if 0 (for 2026 empty columns)
                        PowerData::updateOrCreate(
                            [
                                'ulp_code' => $ulpCode,
                                'month' => $months[$monthIndex],
                                'year' => $year,
                                'data_type' => $dataType,
                            ],
                            [
                                'ulp_name' => $ulpName,
                                'power_va' => $powerVa,
                            ]
                        );
                        $syncedCount++;
                    }
                }
            }

            Log::info("Synced {$syncedCount} power records from Google Sheets");
            return $syncedCount;

        } catch (\Exception $e) {
            Log::error('Error syncing Power Sheets data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Ambil statistik daya
     */
    public function getStatistics($year = null)
    {
        $year = $year ?? 2025;
        
        return [
            'total_ulp' => PowerData::byYear($year)->distinct('ulp_code')->count('ulp_code'),
            'total_power_bulanan' => PowerData::byYear($year)->bulanan()->sum('power_va'),
            'latest_month' => PowerData::byYear($year)->orderBy('id', 'desc')->first()?->month,
            'year' => $year,
        ];
    }

    /**
     * Ambil data per ULP
     */
    public function getDataByUlp($ulpCode, $year = null)
    {
        $year = $year ?? 2025;
        
        $data = PowerData::byUlp($ulpCode)
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
        
        return PowerData::byYear($year)
            ->select('ulp_code', 'ulp_name')
            ->distinct()
            ->orderBy('ulp_code')
            ->get();
    }

    /**
     * Ambil data chart bulanan (data mentah per bulan)
     */
    public function getMonthlyChartData($year = null)
    {
        $year = $year ?? 2025;
        
        // Ambil data bulanan per bulan (bukan kumulatif)
        $data = PowerData::byYear($year)
            ->bulanan()
            ->selectRaw('month, SUM(power_va) as total')
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
            $data = PowerData::byYear($year)
                ->byUlp($ulp->ulp_code)
                ->bulanan()
                ->get()
                ->sortBy(function($item) use ($monthOrder) {
                    return array_search($item->month, $monthOrder);
                });
            
            $result[] = [
                'ulp_code' => $ulp->ulp_code,
                'ulp_name' => $ulp->ulp_name,
                'data' => $data->pluck('power_va')->toArray(),
                'months' => $data->pluck('month')->toArray()
            ];
        }
        
        return $result;
    }
}
