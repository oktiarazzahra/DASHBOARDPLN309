<?php

namespace App\Console\Commands;

use App\Services\CustomerSheetsService;
use Illuminate\Console\Command;

class SyncCustomerData extends Command
{
    protected $signature = 'customers:sync {--year= : Year to display statistics for}';
    protected $description = 'Sync data pelanggan dari Google Sheets ke database';

    protected $customerService;

    public function __construct(CustomerSheetsService $customerService)
    {
        parent::__construct();
        $this->customerService = $customerService;
    }

    public function handle()
    {
        $this->info('Memulai sync data pelanggan dari Google Sheets...');

        try {
            $syncedCount = $this->customerService->syncToDatabase();
            
            $this->info("✓ Berhasil sync {$syncedCount} data pelanggan dari Google Sheets");
            
            $year = $this->option('year') ?? 2025;
            $statistics = $this->customerService->getStatistics($year);
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total ULP', $statistics['total_ulp']],
                    ['Total Pelanggan (Bulanan)', number_format($statistics['total_customers_bulanan'])],
                    ['Total Pelanggan (Kumulatif)', number_format($statistics['total_customers_kumulatif'])],
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
