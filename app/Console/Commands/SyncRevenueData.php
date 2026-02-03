<?php

namespace App\Console\Commands;

use App\Services\RevenueSheetsService;
use Illuminate\Console\Command;

class SyncRevenueData extends Command
{
    protected $signature = 'revenue:sync {--year= : Tahun data yang akan di-sync}';
    protected $description = 'Sync data kWh jual dan Rp pendapatan dari Google Sheets';

    protected $revenueService;

    public function __construct(RevenueSheetsService $revenueService)
    {
        parent::__construct();
        $this->revenueService = $revenueService;
    }

    public function handle()
    {
        $this->info('Memulai sync data revenue dari Google Sheets...');

        try {
            $syncedCount = $this->revenueService->syncToDatabase();
            
            $this->info("✓ Berhasil sync {$syncedCount} data revenue dari Google Sheets");
            
            $year = $this->option('year') ?? 2025;
            $statistics = $this->revenueService->getStatistics($year);

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total ULP', $statistics['total_ulp']],
                    ['Total kWh (Bulanan)', number_format($statistics['total_kwh_bulanan'], 0, ',', '.')],
                    ['Total Rp (Bulanan)', 'Rp ' . number_format($statistics['total_rp_bulanan'], 0, ',', '.')],
                    ['Latest Month', $statistics['latest_month']],
                    ['Year', $statistics['year']],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
