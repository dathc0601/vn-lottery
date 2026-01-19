<?php

namespace App\Jobs;

use App\Models\Province;
use App\Services\StatisticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateStatisticsJob implements ShouldQueue
{
    use Queueable;

    protected ?Province $province;

    /**
     * Create a new job instance.
     */
    public function __construct(?Province $province = null)
    {
        $this->province = $province;
    }

    /**
     * Execute the job.
     */
    public function handle(StatisticsService $statisticsService): void
    {
        if ($this->province) {
            // Generate for specific province
            Log::info("Generating statistics for province: {$this->province->name}");
            $statisticsService->generateStatisticsForProvince($this->province);
        } else {
            // Generate for all provinces
            Log::info("Generating statistics for all provinces");
            $stats = $statisticsService->generateStatisticsForAllProvinces();
            Log::info("Generated {$stats['total_statistics']} statistics for {$stats['total_provinces']} provinces");
        }
    }
}
