<?php

namespace App\Console\Commands;

use App\Services\TarifCustomerSheetsService;
use App\Services\TarifPowerSheetsService;
use App\Services\TarifRevenueSheetsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTarifData extends Command
{
    protected $signature = 'sync:tarif {--year=2025}';
    protected $description = 'Sync tarif data from Google Sheets';
    
    public function handle()
    {
        $year = $this->option('year');
        
        $this->info("Syncing tarif data for year {$year}...");
        
        // Sync Customer Data
        $this->info("Syncing customer data...");
        $customerService = new TarifCustomerSheetsService();
        $customerData = $customerService->getCustomerData($year);
        
        if (!empty($customerData)) {
            DB::table('tarif_customer_data')
                ->where('year', $year)
                ->delete();
            
            DB::table('tarif_customer_data')->insert($customerData);
            $this->info("✓ Synced " . count($customerData) . " customer records");
        } else {
            $this->warn("No customer data found");
        }
        
        // Sync Power Data
        $this->info("Syncing power data...");
        $powerService = new TarifPowerSheetsService();
        $powerData = $powerService->getPowerData($year);
        
        if (!empty($powerData)) {
            DB::table('tarif_power_data')
                ->where('year', $year)
                ->delete();
            
            DB::table('tarif_power_data')->insert($powerData);
            $this->info("✓ Synced " . count($powerData) . " power records");
        } else {
            $this->warn("No power data found");
        }
        
        // Sync Revenue Data
        $this->info("Syncing revenue data...");
        $revenueService = new TarifRevenueSheetsService();
        $revenueData = $revenueService->getRevenueData($year);
        
        if (!empty($revenueData)) {
            DB::table('tarif_revenue_data')
                ->where('year', $year)
                ->delete();
            
            DB::table('tarif_revenue_data')->insert($revenueData);
            $this->info("✓ Synced " . count($revenueData) . " revenue records");
        } else {
            $this->warn("No revenue data found");
        }
        
        $this->info("✓ Tarif data sync completed!");
        
        return Command::SUCCESS;
    }
}
