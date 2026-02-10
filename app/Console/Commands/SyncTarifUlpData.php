<?php

namespace App\Console\Commands;

use App\Services\TarifUlpSheetsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTarifUlpData extends Command
{
    protected $signature = 'sync:tarif-ulp {--year=2025}';
    protected $description = 'Sync tarif data per ULP from Google Sheets (SEMUA/TARIF B.SEL, etc.)';
    
    public function handle()
    {
        $year = $this->option('year');
        
        $this->info("Syncing tarif per ULP data for year {$year}...");
        $this->newLine();
        
        // Delete existing data untuk tahun ini
        $this->info("Deleting existing data for year {$year}...");
        DB::table('tarif_customer_data')->where('year', $year)->whereNotNull('ulp_code')->delete();
        DB::table('tarif_power_data')->where('year', $year)->whereNotNull('ulp_code')->delete();
        DB::table('tarif_revenue_data')->where('year', $year)->whereNotNull('ulp_code')->delete();
        $this->info("✓ Old data deleted");
        $this->newLine();
        
        // Sync new data
        $service = new TarifUlpSheetsService();
        $results = $service->syncToDatabase($year);
        
        $this->newLine();
        $this->info("=== Sync Completed ===");
        $this->info("✓ Customer records: " . $results['customer']);
        $this->info("✓ Power records: " . $results['power']);
        $this->info("✓ kWh records: " . $results['kwh']);
        $this->info("✓ Rp records: " . $results['rp']);
        $this->newLine();
        
        $this->info("Total records synced: " . array_sum($results));
        $this->info("Refresh your browser to see the updated data!");
        
        return 0;
    }
}
