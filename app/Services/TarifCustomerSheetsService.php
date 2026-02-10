<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;

class TarifCustomerSheetsService
{
    protected $client;
    protected $spreadsheetId;
    
    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Dashboard PLN');
        $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('app/google/service-account.json'));
        
        $this->spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
    }
    
    public function getCustomerData($year = 2025)
    {
        $service = new Sheets($this->client);
        
        // Range dari A1 sampai M80 untuk mengcover semua data
        $range = 'PELANGGAN/TARIF!A1:M80';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        
        if (empty($values)) {
            return [];
        }
        
        $result = [];
        
        // Row 4 adalah header bulan (BULANAN | JAN | FEB | ... | DEC)
        // Row 5 onwards adalah data tarif
        
        // Skip rows yang hanya berisi "II" atau "III" (continuation)
        // Skip rows yang berisi "JUMLAH" (subtotal)
        
        $rowOrder = 0; // Counter untuk urutan row
        
        foreach ($values as $index => $row) {
            // Skip header rows (0-4)
            if ($index < 4) {
                continue;
            }
            
            // Skip empty rows
            if (empty($row[0])) {
                continue;
            }
            
            $tarifName = trim($row[0]);
            
            // Skip continuation rows
            if (in_array($tarifName, ['II', 'III', ''])) {
                continue;
            }
            
            // Skip subtotal rows
            if (strpos($tarifName, 'JUMLAH') !== false) {
                continue;
            }
            
            // Increment row order
            $rowOrder++;
            
            // Ekstrak kategori dari nama tarif (S1, R1, B1, I1, P1, T, C, L)
            $category = $this->extractCategory($tarifName);
            
            // Generate tarif code (simplified version of name)
            $tarifCode = $this->generateTarifCode($tarifName);
            
            // Data bulan dimulai dari column index 1 (JAN) sampai 12 (DEC)
            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            
            foreach ($months as $monthIndex => $monthName) {
                $columnIndex = $monthIndex + 1; // +1 karena column 0 adalah nama tarif
                
                $value = isset($row[$columnIndex]) ? $this->cleanNumber($row[$columnIndex]) : 0;
                
                $result[] = [
                    'tarif_code' => $tarifCode,
                    'tarif_name' => $tarifName,
                    'tarif_category' => $category,
                    'row_order' => $rowOrder,
                    'year' => $year,
                    'month' => $monthIndex,
                    'month_name' => $monthName,
                    'total_customers' => $value,
                ];
            }
        }
        
        return $result;
    }
    
    private function extractCategory($tarifName)
    {
        // Ekstrak huruf pertama dari nama tarif
        if (preg_match('/^([SRBIPT])/', $tarifName, $matches)) {
            return $matches[1];
        }
        
        // Untuk C dan L
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
        // Simplify nama tarif menjadi code
        // Contoh: "S 1 / 220 VA" -> "S1/220VA"
        $code = preg_replace('/\s+/', '', $tarifName);
        return $code;
    }
    
    private function cleanNumber($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        // Hapus separator ribuan (titik atau koma)
        $cleaned = str_replace(['.', ',', ' '], '', $value);
        
        return (int)$cleaned;
    }
    
    public function syncToDatabase()
    {
        $data = $this->getCustomerData(2025);
        
        if (!empty($data)) {
            DB::table('tarif_customer_data')->where('year', 2025)->delete();
            
            foreach (array_chunk($data, 100) as $chunk) {
                DB::table('tarif_customer_data')->insert($chunk);
            }
        }
        
        return count($data);
    }
}
