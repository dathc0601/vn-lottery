<?php

namespace App\Console\Commands;

use App\Jobs\FetchAllProvincesJob;
use Illuminate\Console\Command;

class FetchAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:fetch-all
                            {--limit=5 : Number of results to fetch per province}
                            {--region= : Filter by region (north, central, south)}
                            {--fill-gaps : Detect and fill missing dates}
                            {--days=30 : Days to look back when filling gaps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch lottery results for all active provinces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $region = $this->option('region');
        $fillGaps = (bool) $this->option('fill-gaps');
        $daysBack = (int) $this->option('days');

        if ($region && !in_array($region, ['north', 'central', 'south'])) {
            $this->error('Invalid region. Must be: north, central, or south');
            return 1;
        }

        $this->info('Dispatching fetch jobs for all provinces...');

        if ($region) {
            $this->info("Region filter: {$region}");
        }

        if ($fillGaps) {
            $this->info("Mode: Fill gaps (looking back {$daysBack} days)");
        } else {
            $this->info("Limit per province: {$limit}");
        }

        // Dispatch the job
        FetchAllProvincesJob::dispatch($limit, $region, $fillGaps, $daysBack);

        $this->info('✓ Fetch jobs dispatched successfully!');
        $this->info('Run "php artisan queue:work" to process the jobs.');

        return 0;
    }
}
