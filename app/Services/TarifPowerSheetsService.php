<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;

class TarifPowerSheetsService
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
    
    public function getPowerData($year = 2025)
    {
        $service = new Sheets($this->client);
        
        // DAYA/TARIF has only BULANAN (no KOMULATIF)
        // Read up to row 73 to avoid any trailing headers
        $range = 'DAYA/TARIF!A1:AA73';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        
        if (empty($values)) {
            return [];
        }
        
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
                    'total_power' => $value,
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
        
        return (int)$cleaned;
    }
    
    public function syncToDatabase($year = 2025)
    {
        $data = $this->getPowerData($year);
        
        if (!empty($data)) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('tarif_power_data')->upsert(
                    $chunk,
                    ['tarif_code', 'ulp_code', 'year', 'month'],
                    ['tarif_name', 'tarif_category', 'row_order', 'total_power']
                );
            }
        }
        
        return count($data);
    }
}
