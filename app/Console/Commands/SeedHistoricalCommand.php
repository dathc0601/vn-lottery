<?php

namespace App\Console\Commands;

use App\Jobs\FetchLotteryResultsJob;
use App\Models\Province;
use Illuminate\Console\Command;

class SeedHistoricalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:seed-historical
                            {days=30 : Number of days worth of historical data to fetch}
                            {--province= : Optional province code to fetch for}
                            {--region= : Optional region filter (north, central, south)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill historical lottery results data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->argument('days');
        $provinceCode = $this->option('province');
        $region = $this->option('region');

        if ($days < 1 || $days > 500) {
            $this->error('Days must be between 1 and 500');
            return 1;
        }

        if ($region && !in_array($region, ['north', 'central', 'south'])) {
            $this->error('Invalid region. Must be: north, central, or south');
            return 1;
        }

        $this->info("Fetching {$days} days of historical data...");

        if ($provinceCode) {
            // Fetch for specific province
            $province = Province::where('code', $provinceCode)->first();

            if (!$province) {
                $this->error("Province with code '{$provinceCode}' not found!");
                return 1;
            }

            $this->info("Province: {$province->name}");
            FetchLotteryResultsJob::dispatch($province, $days);

            $this->info('✓ Historical data fetch job dispatched!');
        } else {
            // Fetch for all provinces (or filtered by region)
            $query = Province::where('is_active', true);

            if ($region) {
                $query->where('region', $region);
                $this->info("Region filter: {$region}");
            }

            $provinces = $query->get();
            $count = $provinces->count();

            $this->info("Dispatching jobs for {$count} provinces...");

            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($provinces as $province) {
                FetchLotteryResultsJob::dispatch($province, $days);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("✓ Dispatched {$count} historical data fetch jobs!");
        }

        $this->info('Run "php artisan queue:work" to process the jobs.');

        if ($provinceCode) {
            $this->warn("Note: Fetching {$days} days of data may take some time.");
        } else {
            $totalCount = $provinces->count();
            $this->warn("Note: Fetching {$days} days × {$totalCount} provinces may take some time.");
        }

        return 0;
    }
}
