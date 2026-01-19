<?php

namespace App\Jobs;

use App\Models\Province;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchAllProvincesJob implements ShouldQueue
{
    use Queueable;

    protected int $limitNum;
    protected ?string $region;

    /**
     * Create a new job instance.
     */
    public function __construct(int $limitNum = 5, ?string $region = null)
    {
        $this->limitNum = $limitNum;
        $this->region = $region;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('FetchAllProvincesJob started', [
            'limit_num' => $this->limitNum,
            'region' => $this->region ?? 'all',
        ]);

        // Get active provinces
        $query = Province::where('is_active', true);

        // Filter by region if specified
        if ($this->region) {
            $query->where('region', $this->region);
        }

        $provinces = $query->get();

        Log::info('Found provinces to fetch', [
            'count' => $provinces->count(),
        ]);

        // Dispatch individual fetch jobs for each province
        $dispatchedCount = 0;
        foreach ($provinces as $province) {
            FetchLotteryResultsJob::dispatch($province, $this->limitNum);
            $dispatchedCount++;
        }

        Log::info('FetchAllProvincesJob completed', [
            'dispatched_jobs' => $dispatchedCount,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('FetchAllProvincesJob failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
