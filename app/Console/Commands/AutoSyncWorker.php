<?php

namespace App\Console\Commands;

use App\Services\CustomerSheetsService;
use App\Services\PowerSheetsService;
use App\Services\RevenueSheetsService;
use App\Services\TarifCustomerSheetsService;
use App\Services\TarifPowerSheetsService;
use App\Services\TarifRevenueSheetsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AutoSyncWorker extends Command
{
    protected $signature = 'sync:worker {--interval=10}';
    protected $description = 'Background worker yang auto-sync data dari Google Sheets setiap X detik';
    
    public function handle()
    {
        $interval = $this->option('interval');
        
        $this->info("🔄 Auto-sync worker started!");
        $this->info("Checking Google Sheets every {$interval} seconds...");
        $this->info("Press Ctrl+C to stop\n");
        
        while (true) {
            try {
                $this->syncAllData();
                
                // Tunggu sesuai interval
                sleep($interval);
                
            } catch (\Exception $e) {
                $this->error("Error during sync: " . $e->getMessage());
                sleep($interval);
            }
        }
    }
    
    protected function syncAllData()
    {
        $startTime = microtime(true);
        $timestamp = now()->format('H:i:s');
        
        $this->line("[$timestamp] Syncing data...");
        
        // Sync Per ULP Data
        try {
            $customerService = new CustomerSheetsService();
            $customerService->syncToDatabase();
            
            $powerService = new PowerSheetsService();
            $powerService->syncToDatabase();
            
            $revenueService = new RevenueSheetsService();
            $revenueService->syncToDatabase();
            
            $this->info("  ✓ Per ULP synced");
        } catch (\Exception $e) {
            $this->warn("  ⚠ Per ULP sync failed: " . $e->getMessage());
        }
        
        // Sync Per Tarif Data
        try {
            $tarifCustomerService = new TarifCustomerSheetsService();
            $tarifCustomerService->syncToDatabase();
            
            $tarifPowerService = new TarifPowerSheetsService();
            $tarifPowerService->syncToDatabase();
            
            $tarifRevenueService = new TarifRevenueSheetsService();
            $tarifRevenueService->syncToDatabase();
            
            $this->info("  ✓ Per Tarif synced");
        } catch (\Exception $e) {
            $this->warn("  ⚠ Per Tarif sync failed: " . $e->getMessage());
        }
        
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->line("  Completed in {$duration}ms\n");
    }
}
