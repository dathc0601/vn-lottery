<?php

namespace App\Console\Commands;

use App\Jobs\GenerateStatisticsJob;
use App\Models\Province;
use Illuminate\Console\Command;

class GenerateStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:generate-stats
                            {province_code? : Optional province code to generate stats for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate number frequency statistics for lottery results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provinceCode = $this->argument('province_code');

        if ($provinceCode) {
            // Generate for specific province
            $province = Province::where('code', $provinceCode)->first();

            if (!$province) {
                $this->error("Province with code '{$provinceCode}' not found!");
                return 1;
            }

            $this->info("Generating statistics for: {$province->name}");
            GenerateStatisticsJob::dispatch($province);

            $this->info('✓ Statistics generation job dispatched!');
        } else {
            // Generate for all provinces
            $this->info('Generating statistics for all provinces...');
            GenerateStatisticsJob::dispatch();

            $this->info('✓ Statistics generation job dispatched!');
        }

        $this->info('Run "php artisan queue:work" to process the job.');

        return 0;
    }
}
