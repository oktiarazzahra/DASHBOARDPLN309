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
     * Parse customer data dari sheet per ULP
     * Struktur: Row 3 = JUMLAH PELANGGAN header, Row 4 = BULANAN header
     */
    private function getCustomerDataFromSheet($sheetName, $ulpCode, $year)
    {
        $service = new Sheets($this->client);
        
        try {
            // Ambil range yang cukup besar untuk cover semua data
            $range = "{$sheetName}!A1:M80";
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                Log::warning("No data found in sheet: {$sheetName}");
                return [];
            }
            
            return $this->parseDataSection($values, $ulpCode, $year, 'JUMLAH PELANGGAN');
            
        } catch (\Exception $e) {
            Log::error("Error reading sheet {$sheetName}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Parse power data dari sheet per ULP
     */
    private function getPowerDataFromSheet($sheetName, $ulpCode, $year)
    {
        $service = new Sheets($this->client);
        
        try {
            // Daya Tersambung ada di kolom O-AA (index 14-26)
            $range = "{$sheetName}!O1:AA80";
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            return $this->parseDataSection($values, $ulpCode, $year, 'DAYA TERSAMBUNG', true);
            
        } catch (\Exception $e) {
            Log::error("Error reading power data from {$sheetName}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Parse KWH data
     */
    private function getKwhDataFromSheet($sheetName, $ulpCode, $year)
    {
        $service = new Sheets($this->client);
        
        try {
            // KWH ada di kolom AC-AO (index 28-40)
            $range = "{$sheetName}!AC1:AO80";
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            return $this->parseDataSection($values, $ulpCode, $year, 'KWH JUAL', false, 'kwh');
            
        } catch (\Exception $e) {
            Log::error("Error reading KWH data from {$sheetName}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Parse RP Pendapatan data
     */
    private function getRpDataFromSheet($sheetName, $ulpCode, $year)
    {
        $service = new Sheets($this->client);
        
        try {
            // RP ada di kolom AQ-BC (index 42-54)
            $range = "{$sheetName}!AQ1:BC80";
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            return $this->parseDataSection($values, $ulpCode, $year, 'RP PENDAPATAN', false, 'rp');
            
        } catch (\Exception $e) {
            Log::error("Error reading RP data from {$sheetName}: " . $e->getMessage());
            return [];
        }
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
            
            // Skip continuation rows dan subtotal
            if (in_array($tarifName, ['II', 'III', '']) || strpos($tarifName, 'JUMLAH') !== false) {
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
                foreach (array_chunk($data['customer'], 100) as $chunk) {
                    DB::table('tarif_customer_data')->insert($chunk);
                }
                $totalCustomer += count($data['customer']);
            }
            
            // Sync power data
            if (!empty($data['power'])) {
                foreach (array_chunk($data['power'], 100) as $chunk) {
                    DB::table('tarif_power_data')->insert($chunk);
                }
                $totalPower += count($data['power']);
            }
            
            // Sync KWH data
            if (!empty($data['kwh'])) {
                foreach (array_chunk($data['kwh'], 100) as $chunk) {
                    DB::table('tarif_revenue_data')->insert($chunk);
                }
                $totalKwh += count($data['kwh']);
            }
            
            // Sync RP data
            if (!empty($data['rp'])) {
                foreach (array_chunk($data['rp'], 100) as $chunk) {
                    DB::table('tarif_revenue_data')->insert($chunk);
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
