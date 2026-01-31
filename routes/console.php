<?php

use App\Jobs\FetchAllProvincesJob;
use App\Jobs\GeneratePredictionsJob;
use App\Jobs\GenerateStatisticsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// Lottery Scheduler Configuration
// ============================================
// Official Drawing Times:
// - South (Miền Nam): 16:15 → 16:35
// - Central (Miền Trung): 17:15 → 17:35
// - North (Miền Bắc): 18:15 → 18:35
// ============================================

// Fetch lottery results hourly
Schedule::command('lottery:fetch-all')
    ->hourly()
    ->name('fetch-all-lottery-results-hourly')
    ->onOneServer()
    ->withoutOverlapping();

// Generate statistics for all provinces - Weekly on Sunday at midnight
Schedule::job(new GenerateStatisticsJob())
    ->weekly()
    ->sundays()
    ->at('00:00')
    ->name('generate-statistics-weekly')
    ->onOneServer()
    ->withoutOverlapping();

// ============================================
// Vietlott Scheduler Configuration
// ============================================
// Vietlott Drawing Time: 18:00 daily
// ============================================

// Fetch Vietlott results - Daily at 19:00 (1 hour after draw completion)
Schedule::command('vietlott:fetch')
    ->dailyAt('19:00')
    ->name('fetch-vietlott-daily')
    ->onOneServer()
    ->withoutOverlapping();

// ============================================
// Prediction Scheduler Configuration
// ============================================
// Predictions are generated daily at 02:00 AM
// based on previous day's lottery results
// ============================================

// Generate daily predictions at 2 AM
Schedule::job(new GeneratePredictionsJob())
    ->dailyAt('02:00')
    ->name('generate-daily-predictions')
    ->onOneServer()
    ->withoutOverlapping();
