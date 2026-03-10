<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-sync data dari Google Sheets setiap 10 menit
// Data Per ULP (Customer, Power, Revenue)
Schedule::command('data:auto-sync --year=2025')->everyTenMinutes()->withoutOverlapping();
Schedule::command('data:auto-sync --year=2026')->everyTenMinutes()->withoutOverlapping();

// Data Per Tarif
Schedule::command('sync:tarif --year=2025')->everyTenMinutes()->withoutOverlapping();
Schedule::command('sync:tarif --year=2026')->everyTenMinutes()->withoutOverlapping();

// Data Tarif Per ULP
Schedule::command('sync:tarif-ulp --year=2025')->everyTenMinutes()->withoutOverlapping();
Schedule::command('sync:tarif-ulp --year=2026')->everyTenMinutes()->withoutOverlapping();
