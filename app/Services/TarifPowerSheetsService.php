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
        
        $range = 'DAYA/TARIF!A1:M80';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        
        if (empty($values)) {
            return [];
        }
        
        $result = [];
        
        $rowOrder = 0; // Counter untuk urutan row
        
        foreach ($values as $index => $row) {
            if ($index < 4) {
                continue;
            }
            
            if (empty($row[0])) {
                continue;
            }
            
            $tarifName = trim($row[0]);
            
            if (in_array($tarifName, ['II', 'III', ''])) {
                continue;
            }
            
            if (strpos($tarifName, 'JUMLAH') !== false) {
                continue;
            }
            
            $rowOrder++;
            
            $category = $this->extractCategory($tarifName);
            $tarifCode = $this->generateTarifCode($tarifName);
            
            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            
            foreach ($months as $monthIndex => $monthName) {
                $columnIndex = $monthIndex + 1;
                
                $value = isset($row[$columnIndex]) ? $this->cleanNumber($row[$columnIndex]) : 0;
                
                $result[] = [
                    'tarif_code' => $tarifCode,
                    'tarif_name' => $tarifName,
                    'tarif_category' => $category,
                    'row_order' => $rowOrder,
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
    
    public function syncToDatabase()
    {
        $data = $this->getPowerData(2025);
        
        if (!empty($data)) {
            DB::table('tarif_power_data')->where('year', 2025)->delete();
            
            foreach (array_chunk($data, 100) as $chunk) {
                DB::table('tarif_power_data')->insert($chunk);
            }
        }
        
        return count($data);
    }
}
