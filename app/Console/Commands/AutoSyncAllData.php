<?php

namespace App\Console\Commands;

use App\Events\DataUpdatedEvent;
use App\Services\CustomerSheetsService;
use App\Services\PowerSheetsService;
use App\Services\RevenueSheetsService;
use Illuminate\Console\Command;

class AutoSyncAllData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'data:auto-sync {--year=2025}';

    /**
     * The console command description.
     */
    protected $description = 'Auto-sync all data from Google Sheets and broadcast updates';

    protected $customerService;
    protected $powerService;
    protected $revenueService;

    public function __construct(
        CustomerSheetsService $customerService,
        PowerSheetsService $powerService,
        RevenueSheetsService $revenueService
    ) {
        parent::__construct();
        $this->customerService = $customerService;
        $this->powerService = $powerService;
        $this->revenueService = $revenueService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = (int)$this->option('year');
        $this->info("Starting auto-sync for year {$year}...");

        try {
            // Sync Customer Data
            $this->info('Syncing customer data...');
            $customerCount = $this->customerService->syncToDatabase();
            $customerStats = $this->customerService->getStatistics($year);
            
            if ($customerCount > 0) {
                event(new DataUpdatedEvent('customer', $year, $customerCount, $customerStats));
                $this->info("✓ Customer data synced: {$customerCount} records");
            }

            // Sync Power Data
            $this->info('Syncing power data...');
            $powerCount = $this->powerService->syncToDatabase();
            $powerStats = $this->powerService->getStatistics($year);
            
            if ($powerCount > 0) {
                event(new DataUpdatedEvent('power', $year, $powerCount, $powerStats));
                $this->info("✓ Power data synced: {$powerCount} records");
            }

            // Sync Revenue Data
            $this->info('Syncing revenue data...');
            $revenueCount = $this->revenueService->syncToDatabase();
            $revenueStats = $this->revenueService->getStatistics($year);
            
            if ($revenueCount > 0) {
                event(new DataUpdatedEvent('revenue', $year, $revenueCount, $revenueStats));
                $this->info("✓ Revenue data synced: {$revenueCount} records");
            }

            // Broadcast complete event jika ada perubahan
            $totalChanges = $customerCount + $powerCount + $revenueCount;
            if ($totalChanges > 0) {
                event(new DataUpdatedEvent('all', $year, $totalChanges, [
                    'customer' => $customerStats,
                    'power' => $powerStats,
                    'revenue' => $revenueStats
                ]));
                $this->info("\n🔥 Broadcasting update to connected clients...");
            }

            $this->info("\n✓ All data synced successfully!");
            $this->table(
                ['Data Type', 'Records Synced'],
                [
                    ['Customer', $customerCount],
                    ['Power', $powerCount],
                    ['Revenue', $revenueCount],
                    ['Total', $customerCount + $powerCount + $revenueCount],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error during auto-sync: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
