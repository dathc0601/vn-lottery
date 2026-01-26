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
    protected bool $fillGaps;
    protected int $daysBack;

    /**
     * Create a new job instance.
     */
    public function __construct(Province $province, int $limitNum = 30, bool $fillGaps = false, int $daysBack = 30)
    {
        $this->province = $province;
        $this->limitNum = $limitNum;
        $this->fillGaps = $fillGaps;
        $this->daysBack = $daysBack;
    }

    /**
     * Execute the job.
     */
    public function handle(LotteryApiService $apiService): void
    {
        Log::info("Fetching lottery results for province: {$this->province->name}", [
            'fill_gaps' => $this->fillGaps,
            'days_back' => $this->daysBack,
        ]);

        // Handle fill-gaps mode
        if ($this->fillGaps) {
            $stored = $apiService->fetchMissingResults($this->province, $this->daysBack);
            Log::info("✓ Filled {$stored} missing results for: {$this->province->name}");
            return;
        }

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
