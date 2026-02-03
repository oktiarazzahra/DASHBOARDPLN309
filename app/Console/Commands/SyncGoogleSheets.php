<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

class SyncGoogleSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheets:sync {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data dari Google Sheets ke database';

    protected $googleSheetsService;

    /**
     * Create a new command instance.
     */
    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sync data dari Google Sheets...');

        try {
            $syncedCount = $this->googleSheetsService->syncToDatabase();
            
            $this->info("✓ Berhasil sync {$syncedCount} data dari Google Sheets");
            
            $statistics = $this->googleSheetsService->getStatistics();
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Records', $statistics['total_records']],
                    ['Active Locations', $statistics['active_locations']],
                    ['Critical Alerts', $statistics['critical_alerts']],
                    ['Total Power (kW)', number_format($statistics['total_power'], 2)],
                    ['Total Energy (kWh)', number_format($statistics['total_energy'], 2)],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
