<?php

namespace App\Console\Commands;

use App\Services\PowerSheetsService;
use Illuminate\Console\Command;

class SyncPowerData extends Command
{
    protected $signature = 'power:sync {--year= : Year to display statistics for}';
    protected $description = 'Sync data daya/power dari Google Sheets ke database';

    protected $powerService;

    public function __construct(PowerSheetsService $powerService)
    {
        parent::__construct();
        $this->powerService = $powerService;
    }

    public function handle()
    {
        $this->info('Memulai sync data daya dari Google Sheets...');

        try {
            $syncedCount = $this->powerService->syncToDatabase();
            
            $this->info("✓ Berhasil sync {$syncedCount} data daya dari Google Sheets");
            
            $year = $this->option('year') ?? 2025;
            $statistics = $this->powerService->getStatistics($year);
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total ULP', $statistics['total_ulp']],
                    ['Total Daya Bulanan (VA)', number_format($statistics['total_power_bulanan'])],
                    ['Total Daya Kumulatif (VA)', number_format($statistics['total_power_kumulatif'])],
                    ['Latest Month', $statistics['latest_month'] ?? 'N/A'],
                    ['Year', $statistics['year']],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}

