<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-sync data setiap 5 menit (bisa diubah sesuai kebutuhan)
Schedule::command('data:auto-sync --year=2025')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('data:auto-sync --year=2026')->everyFiveMinutes()->withoutOverlapping();
