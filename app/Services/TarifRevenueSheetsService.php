<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;

class TarifRevenueSheetsService
{
    protected $client;
    protected $spreadsheetId;
    
    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Dashboard PLN');
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
        
        $this->spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
    }
    
    public function getRevenueData($year = 2025)
    {
        $result = [];
        $keys = []; // Untuk tracking duplikat
        
        // Ambil data kWh Jual
        $kwhData = $this->getKwhJual($year);
        foreach ($kwhData as $data) {
            $key = $data['tarif_code'] . '_' . $data['year'] . '_' . $data['month'] . '_kwh';
            if (!isset($keys[$key])) {
                $result[] = array_merge($data, ['data_type' => 'kwh']);
                $keys[$key] = true;
            }
        }
        
        // Ambil data Rp Pendapatan
        $rpData = $this->getRpPendapatan($year);
        foreach ($rpData as $data) {
            $key = $data['tarif_code'] . '_' . $data['year'] . '_' . $data['month'] . '_rp';
            if (!isset($keys[$key])) {
                $result[] = array_merge($data, ['data_type' => 'rp']);
                $keys[$key] = true;
            }
        }
        
        return $result;
    }
    
    private function getKwhJual($year)
    {
        $service = new Sheets($this->client);
        
        // Read up to row 73 (BULANAN section only, rows 4-71 are tarif data)
        // KOMULATIF section starts at row 74 — we skip it
        $range = 'KWHJUAL/TARIF!A1:AA73';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        
        if (empty($values)) {
            return [];
        }
        
        // 2025: tarif name at col 0, data at col 1-12
        // 2026: tarif name at col 14, data at col 15-26
        $dataColOffset = ($year == 2026) ? 15 : 1;
        $nameColOffset = ($year == 2026) ? 14 : 0;
        
        $result = [];
        $rowOrder = 0;
        
        foreach ($values as $index => $row) {
            if ($index < 4) {
                continue;
            }
            
            if (empty($row[$nameColOffset])) {
                continue;
            }
            
            $tarifName = trim($row[$nameColOffset]);
            
            // Skip non-tarif rows (headers, section labels, subtotals)
            if (in_array($tarifName, ['II', 'III', 'BULANAN', 'KOMULATIF', ''])) {
                continue;
            }
            
            if (strpos($tarifName, 'JUMLAH') !== false) {
                continue;
            }
            
            // Skip anything that looks like a title row
            if (strpos($tarifName, 'KALTIMRA') !== false) {
                continue;
            }
            
            $rowOrder++;
            
            $category = $this->extractCategory($tarifName);
            $tarifCode = $this->generateTarifCode($tarifName);
            
            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            
            foreach ($months as $monthIndex => $monthName) {
                $columnIndex = $dataColOffset + $monthIndex;
                
                $value = isset($row[$columnIndex]) ? $this->cleanNumber($row[$columnIndex]) : 0;
                
                $result[] = [
                    'tarif_code' => $tarifCode,
                    'tarif_name' => $tarifName,
                    'tarif_category' => $category,
                    'row_order' => $rowOrder,
                    'ulp_code' => '',
                    'year' => $year,
                    'month' => $monthIndex,
                    'month_name' => $monthName,
                    'value' => $value,
                ];
            }
        }
        
        return $result;
    }
    
    private function getRpPendapatan($year)
    {
        $service = new Sheets($this->client);
        
        // Read up to row 73 (BULANAN section only, rows 4-71 are tarif data)
        // KOMULATIF section starts at row 74 — we skip it
        $range = 'PENDAPATAN/TARIF!A1:AA73';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        
        if (empty($values)) {
            return [];
        }
        
        // 2025: tarif name at col 0, data at col 1-12
        // 2026: tarif name at col 14, data at col 15-26
        $dataColOffset = ($year == 2026) ? 15 : 1;
        $nameColOffset = ($year == 2026) ? 14 : 0;
        
        $result = [];
        $rowOrder = 0;
        
        foreach ($values as $index => $row) {
            if ($index < 4) {
                continue;
            }
            
            if (empty($row[$nameColOffset])) {
                continue;
            }
            
            $tarifName = trim($row[$nameColOffset]);
            
            // Skip non-tarif rows (headers, section labels, subtotals)
            if (in_array($tarifName, ['II', 'III', 'BULANAN', 'KOMULATIF', ''])) {
                continue;
            }
            
            if (strpos($tarifName, 'JUMLAH') !== false) {
                continue;
            }
            
            // Skip anything that looks like a title row
            if (strpos($tarifName, 'KALTIMRA') !== false) {
                continue;
            }
            
            $rowOrder++;
            
            $category = $this->extractCategory($tarifName);
            $tarifCode = $this->generateTarifCode($tarifName);
            
            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            
            foreach ($months as $monthIndex => $monthName) {
                $columnIndex = $dataColOffset + $monthIndex;
                
                $value = isset($row[$columnIndex]) ? $this->cleanNumber($row[$columnIndex]) : 0;
                
                $result[] = [
                    'tarif_code' => $tarifCode,
                    'tarif_name' => $tarifName,
                    'tarif_category' => $category,
                    'row_order' => $rowOrder,
                    'ulp_code' => '',
                    'year' => $year,
                    'month' => $monthIndex,
                    'month_name' => $monthName,
                    'value' => $value,
                ];
            }
        }
        
        return $result;
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
        
        $cleaned = str_replace(['.', ',', ' '], '', $value);
        
        return (float)$cleaned;
    }
    
    public function syncToDatabase($year = 2025)
    {
        $data = $this->getRevenueData($year);
        
        if (!empty($data)) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('tarif_revenue_data')->upsert(
                    $chunk,
                    ['tarif_code', 'ulp_code', 'year', 'month', 'data_type'],
                    ['tarif_name', 'tarif_category', 'row_order', 'value']
                );
            }
        }
        
        return count($data);
    }
}
