<?php

namespace App\Console\Commands;

use App\Models\Province;
use App\Services\LotteryApiService;
use Illuminate\Console\Command;

class FetchLotteryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:fetch {province_code?} {--limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch lottery results for a specific province';

    /**
     * Execute the console command.
     */
    public function handle(LotteryApiService $apiService)
    {
        $provinceCode = $this->argument('province_code');
        $limit = (int) $this->option('limit');

        if (!$provinceCode) {
            // Show list of provinces
            $this->info('Available provinces:');
            $provinces = Province::orderBy('region')->orderBy('sort_order')->get();

            foreach ($provinces->groupBy('region') as $region => $regionProvinces) {
                $this->info("\n" . strtoupper($region) . ":");
                foreach ($regionProvinces as $province) {
                    $this->line("  {$province->code} - {$province->name}");
                }
            }

            $this->info("\nUsage: php artisan lottery:fetch {province_code} --limit={number}");
            return 0;
        }

        // Find province
        $province = Province::where('code', $provinceCode)->first();

        if (!$province) {
            $this->error("Province with code '{$provinceCode}' not found!");
            return 1;
        }

        $this->info("Fetching lottery results for: {$province->name} ({$province->code})");
        $this->info("Limit: {$limit} results");

        // Fetch and store results with source routing
        if ($province->code === 'miba') {
            $stored = $apiService->fetchAndStoreXSMBResults($province, $limit);
        } elseif ($province->region === 'central') {
            $stored = $apiService->fetchAndStoreXSMTResults($province, $limit);
        } else {
            $stored = $apiService->fetchAndStoreXSMNResults($province, $limit);
        }

        if ($stored > 0) {
            $this->info("✓ Successfully fetched and stored {$stored} results!");
        } else {
            $this->warn("⚠ No results were stored. Check the API logs for details.");
        }

        return 0;
    }
}
