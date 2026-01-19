<?php

use App\Jobs\FetchAllProvincesJob;
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

// Fetch South region - Daily at 16:45 (10 min after 16:35 draw completion)
Schedule::job(new FetchAllProvincesJob(limitNum: 1, region: 'south'))
    ->dailyAt('16:45')
    ->name('fetch-xsmn-daily')
    ->onOneServer()
    ->withoutOverlapping();

// Fetch Central region - Daily at 17:45 (10 min after 17:35 draw completion)
Schedule::job(new FetchAllProvincesJob(limitNum: 1, region: 'central'))
    ->dailyAt('17:45')
    ->name('fetch-xsmt-daily')
    ->onOneServer()
    ->withoutOverlapping();

// Fetch North region - Daily at 18:45 (10 min after 18:35 draw completion)
Schedule::job(new FetchAllProvincesJob(limitNum: 1, region: 'north'))
    ->dailyAt('18:45')
    ->name('fetch-xsmb-daily')
    ->onOneServer()
    ->withoutOverlapping();

// Backup fetch all regions - Daily at 21:00 to catch any missed results
Schedule::job(new FetchAllProvincesJob(limitNum: 2, region: null))
    ->dailyAt('21:00')
    ->name('fetch-all-backup')
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

// Optional: Daily statistics generation (commented out by default)
// Uncomment if you want daily statistics updates instead of weekly
// Schedule::job(new GenerateStatisticsJob())
//     ->dailyAt('01:00')
//     ->name('generate-statistics-daily')
//     ->onOneServer()
//     ->withoutOverlapping();
