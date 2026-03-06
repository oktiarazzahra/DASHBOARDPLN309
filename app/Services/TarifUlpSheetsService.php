<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TarifUlpSheetsService
{
    protected $client;
    protected $spreadsheetId;
    protected $sheetMappings;
    
    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Dashboard PLN');
        $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('app/google/service-account.json'));
        
        $this->spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
        
        // Mapping sheet names berdasarkan screenshot yang diberikan
        $this->sheetMappings = [
            'B.SEL' => 'SEMUA/TARIF B.SEL',
            'B.UTARA' => 'SEMUA/TARIF B.UTARA',
            'SAMBOJA' => 'SEMUA/TARIF SAMBOJA',
            'PETUNG' => 'SEMUA/TARIF PETUNG',
            'LONGIKIS' => ' SEMUA/TARIF LONGIKIS', // Ada spasi di depan
            'T.G.' => 'SEMUA/TARIF T.G.',
        ];
    }
    
    /**
     * Get all tarif data per ULP
     */
    public function getAllTarifUlpData($year = 2025)
    {
        $allData = [];
        
        foreach ($this->sheetMappings as $ulpCode => $sheetName) {
            Log::info("Processing sheet: {$sheetName} for ULP: {$ulpCode}");
            
            $customerData = $this->getCustomerDataFromSheet($sheetName, $ulpCode, $year);
            $powerData = $this->getPowerDataFromSheet($sheetName, $ulpCode, $year);
            $kwhData = $this->getKwhDataFromSheet($sheetName, $ulpCode, $year);
            $rpData = $this->getRpDataFromSheet($sheetName, $ulpCode, $year);
            
            $allData[$ulpCode] = [
                'customer' => $customerData,
                'power' => $powerData,
                'kwh' => $kwhData,
                'rp' => $rpData
            ];
        }
        
        return $allData;
    }
    
    /**
     * Mapping kolom berdasarkan tahun.
     * Struktur sheet per ULP:
     *   2025: Customer A-M(0-12), Power O-AA(14-26), KWH AC-AO(28-40), RP AQ-BC(42-54)
     *   2026: Customer BE-BQ(56-68), Power BS-CE(70-82), KWH CG-CS(84-96), RP CU-DG(98-110)
     *   (setiap tahun offset +56 kolom dari tahun sebelumnya)
     */
    private function getColumnRange($year, $type)
    {
        // Definisi range per tipe per tahun
        $ranges = [
            2025 => [
                'customer' => ['range' => 'A1:M80',   'keyword' => 'PELANGGAN'],
                'power'    => ['range' => 'O1:AA80',  'keyword' => 'DAYA'],
                'kwh'      => ['range' => 'AC1:AO80', 'keyword' => 'KWH'],
                'rp'       => ['range' => 'AQ1:BC80', 'keyword' => 'RUPIAH'],
            ],
            2026 => [
                'customer' => ['range' => 'BE1:BQ80', 'keyword' => 'PELANGGAN'],
                'power'    => ['range' => 'BS1:CE80', 'keyword' => 'DAYA'],
                'kwh'      => ['range' => 'CG1:CS80', 'keyword' => 'KWH'],
                'rp'       => ['range' => 'CU1:DG80', 'keyword' => 'RUPIAH'],
            ],
        ];
        return $ranges[$year][$type] ?? null;
    }

    /**
     * Ambil data dari sheet, validasi keyword tahun di header row 0.
     * Return empty jika kolom untuk tahun ini belum ada di sheet.
     */
    private function fetchSheetSection($sheetName, $year, $type)
    {
        $colDef = $this->getColumnRange($year, $type);
        if (!$colDef) {
            Log::warning("No column definition for year={$year} type={$type}");
            return [];
        }

        $service = new Sheets($this->client);
        try {
            $response = $service->spreadsheets_values->get(
                $this->spreadsheetId,
                "{$sheetName}!{$colDef['range']}"
            );
            $values = $response->getValues();

            if (empty($values)) {
                return [];
            }

            // Validasi: header row 0 harus mengandung tahun yang diminta
            // (misal "JUMLAH PELANGGAN 2025 ..." atau "JUMLAH PELANGGAN 2026 ...")
            $headerRow = implode(' ', $values[0] ?? []);
            if (!str_contains($headerRow, (string)$year)) {
                Log::info("Sheet {$sheetName} col {$colDef['range']}: header tidak mengandung tahun {$year}, skip.");
                return [];
            }

            return $values;
        } catch (\Exception $e) {
            Log::error("Error reading {$sheetName} [{$type}/{$year}]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse customer data dari sheet per ULP
     */
    private function getCustomerDataFromSheet($sheetName, $ulpCode, $year)
    {
        $values = $this->fetchSheetSection($sheetName, $year, 'customer');
        if (empty($values)) return [];
        return $this->parseDataSection($values, $ulpCode, $year, 'JUMLAH PELANGGAN');
    }
    
    /**
     * Parse power data dari sheet per ULP
     */
    private function getPowerDataFromSheet($sheetName, $ulpCode, $year)
    {
        $values = $this->fetchSheetSection($sheetName, $year, 'power');
        if (empty($values)) return [];
        return $this->parseDataSection($values, $ulpCode, $year, 'DAYA TERSAMBUNG', true);
    }
    
    /**
     * Parse KWH data
     */
    private function getKwhDataFromSheet($sheetName, $ulpCode, $year)
    {
        $values = $this->fetchSheetSection($sheetName, $year, 'kwh');
        if (empty($values)) return [];
        return $this->parseDataSection($values, $ulpCode, $year, 'KWH JUAL', false, 'kwh');
    }
    
    /**
     * Parse RP Pendapatan data
     */
    private function getRpDataFromSheet($sheetName, $ulpCode, $year)
    {
        $values = $this->fetchSheetSection($sheetName, $year, 'rp');
        if (empty($values)) return [];
        return $this->parseDataSection($values, $ulpCode, $year, 'RP PENDAPATAN', false, 'rp');
    }
    
    /**
     * Generic parser untuk semua tipe data
     */
    private function parseDataSection($values, $ulpCode, $year, $headerKeyword, $isPower = false, $dataType = null)
    {
        $result = [];
        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        
        // Di sheet SEMUA/TARIF, BULANAN ada di row 1 (index 0)
        // Row 2 (index 1) adalah header bulan (JAN, FEB, ...)
        // Data dimulai dari row 3 (index 2)
        
        $startDataRow = 2;
        $rowOrder = 0; // Counter untuk urutan row
        
        // Parse setiap baris tarif
        foreach ($values as $index => $row) {
            // Skip header rows (0-1)
            if ($index < $startDataRow) {
                continue;
            }
            
            // Skip empty rows
            if (empty($row[0])) {
                continue;
            }
            
            $tarifName = trim($row[0]);
            
            // Skip header/continuation rows dan subtotal
            if (in_array($tarifName, ['II', 'III', '', 'BULANAN']) || strpos($tarifName, 'JUMLAH') !== false) {
                continue;
            }
            
            // Increment row order untuk setiap tarif yang valid
            $rowOrder++;
            
            // Extract category dan generate code
            $category = $this->extractCategory($tarifName);
            $tarifCode = $this->generateTarifCode($tarifName);
            
            // Parse data per bulan
            foreach ($months as $monthIndex => $monthName) {
                $columnIndex = $monthIndex + 1; // +1 karena column 0 adalah nama tarif
                
                $value = isset($row[$columnIndex]) ? $this->cleanNumber($row[$columnIndex]) : 0;
                
                if ($isPower) {
                    // Data power
                    $result[] = [
                        'ulp_code' => $ulpCode,
                        'ulp_name' => $this->getUlpName($ulpCode),
                        'tarif_code' => $tarifCode,
                        'tarif_name' => $tarifName,
                        'tarif_category' => $category,
                        'row_order' => $rowOrder,
                        'year' => $year,
                        'month' => $monthIndex,
                        'month_name' => $monthName,
                        'total_power' => $value,
                    ];
                } elseif ($dataType) {
                    // Data revenue (kwh atau rp)
                    $result[] = [
                        'ulp_code' => $ulpCode,
                        'ulp_name' => $this->getUlpName($ulpCode),
                        'tarif_code' => $tarifCode,
                        'tarif_name' => $tarifName,
                        'tarif_category' => $category,
                        'row_order' => $rowOrder,
                        'year' => $year,
                        'month' => $monthIndex,
                        'month_name' => $monthName,
                        'data_type' => $dataType,
                        'value' => $value,
                    ];
                } else {
                    // Data customer
                    $result[] = [
                        'ulp_code' => $ulpCode,
                        'ulp_name' => $this->getUlpName($ulpCode),
                        'tarif_code' => $tarifCode,
                        'tarif_name' => $tarifName,
                        'row_order' => $rowOrder,
                        'tarif_category' => $category,
                        'year' => $year,
                        'month' => $monthIndex,
                        'month_name' => $monthName,
                        'total_customers' => $value,
                    ];
                }
            }
        }
        
        return $result;
    }
    
    private function getUlpName($ulpCode)
    {
        $mapping = [
            'B.SEL' => 'BALIKPAPAN SELATAN',
            'B.UTARA' => 'BALIKPAPAN UTARA',
            'SAMBOJA' => 'SAMBOJA',
            'PETUNG' => 'PETUNG',
            'LONGIKIS' => 'LONGIKIS',
            'T.G.' => 'TANAH GROGOT',
        ];
        
        return $mapping[$ulpCode] ?? $ulpCode;
    }
    
    private function extractCategory($tarifName)
    {
        if (preg_match('/^([SRBIPT])/', $tarifName, $matches)) {
            return $matches[1];
        }
        
        if (strpos($tarifName, 'C /') === 0) {
            return 'C';
        }
        if (strpos($tarifName, 'L /') === 0) {
            return 'L';
        }
        
        return 'OTHER';
    }
    
    private function generateTarifCode($tarifName)
    {
        $code = preg_replace('/\s+/', '', $tarifName);
        return $code;
    }
    
    private function cleanNumber($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        // Hapus separator ribuan
        $cleaned = str_replace(['.', ',', ' '], '', $value);
        
        // Handle decimal untuk revenue
        if (strpos($cleaned, '.') !== false) {
            return (float)$cleaned;
        }
        
        return (int)$cleaned;
    }
    
    /**
     * Sync semua data ke database
     */
    public function syncToDatabase($year = 2025)
    {
        $allData = $this->getAllTarifUlpData($year);
        
        $totalCustomer = 0;
        $totalPower = 0;
        $totalKwh = 0;
        $totalRp = 0;
        
        foreach ($allData as $ulpCode => $data) {
            // Sync customer data
            if (!empty($data['customer'])) {
                foreach (array_chunk($data['customer'], 200) as $chunk) {
                    DB::table('tarif_customer_data')->upsert(
                        $chunk,
                        ['tarif_code', 'ulp_code', 'year', 'month'],
                        ['tarif_name', 'tarif_category', 'row_order', 'ulp_name', 'total_customers']
                    );
                }
                $totalCustomer += count($data['customer']);
            }
            
            // Sync power data
            if (!empty($data['power'])) {
                foreach (array_chunk($data['power'], 200) as $chunk) {
                    DB::table('tarif_power_data')->upsert(
                        $chunk,
                        ['tarif_code', 'ulp_code', 'year', 'month'],
                        ['tarif_name', 'tarif_category', 'row_order', 'ulp_name', 'total_power']
                    );
                }
                $totalPower += count($data['power']);
            }
            
            // Sync KWH data
            if (!empty($data['kwh'])) {
                foreach (array_chunk($data['kwh'], 200) as $chunk) {
                    DB::table('tarif_revenue_data')->upsert(
                        $chunk,
                        ['tarif_code', 'ulp_code', 'year', 'month', 'data_type'],
                        ['tarif_name', 'tarif_category', 'row_order', 'ulp_name', 'value']
                    );
                }
                $totalKwh += count($data['kwh']);
            }
            
            // Sync RP data
            if (!empty($data['rp'])) {
                foreach (array_chunk($data['rp'], 200) as $chunk) {
                    DB::table('tarif_revenue_data')->upsert(
                        $chunk,
                        ['tarif_code', 'ulp_code', 'year', 'month', 'data_type'],
                        ['tarif_name', 'tarif_category', 'row_order', 'ulp_name', 'value']
                    );
                }
                $totalRp += count($data['rp']);
            }
        }
        
        return [
            'customer' => $totalCustomer,
            'power' => $totalPower,
            'kwh' => $totalKwh,
            'rp' => $totalRp
        ];
    }
}
