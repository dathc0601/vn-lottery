<?php

namespace App\Jobs;

use App\Models\Province;
use App\Services\LotteryApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchLotteryResultsJob implements ShouldQueue
{
    use Queueable;

    protected Province $province;
    protected int $limitNum;

    /**
     * Create a new job instance.
     */
    public function __construct(Province $province, int $limitNum = 30)
    {
        $this->province = $province;
        $this->limitNum = $limitNum;
    }

    /**
     * Execute the job.
     */
    public function handle(LotteryApiService $apiService): void
    {
        Log::info("Fetching lottery results for province: {$this->province->name}");

        // Check if this is XSMB Hà Nội and we're fetching today's result
        if ($this->province->code === 'miba' && $this->limitNum === 1) {
            // Use new API for today's XSMB
            $success = $apiService->fetchAndStoreXSMBToday($this->province);

            if ($success) {
                Log::info("✓ Fetched today's XSMB from new API: {$this->province->name}");
            } else {
                Log::info("✗ Failed to fetch XSMB from new API, fallback completed: {$this->province->name}");
            }
        } else {
            // Use old API for historical data or other provinces
            $stored = $apiService->fetchAndStoreResults($this->province, $this->limitNum);
            Log::info("✓ Fetched {$stored} results for: {$this->province->name}");
        }
    }
}
