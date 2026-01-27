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

        // XSMB (miba): use GitHub CSV source
        if ($this->province->code === 'miba') {
            $stored = $apiService->fetchAndStoreXSMBResults($this->province, $this->limitNum);
            Log::info("✓ Fetched {$stored} XSMB results from GitHub: {$this->province->name}");
        } elseif ($this->province->region === 'central') {
            // XSMT: use RSS feed
            $stored = $apiService->fetchAndStoreXSMTResults($this->province, $this->limitNum);
            Log::info("✓ Fetched {$stored} XSMT results from RSS: {$this->province->name}");
        } else {
            // XSMN: use RSS feed
            $stored = $apiService->fetchAndStoreXSMNResults($this->province, $this->limitNum);
            Log::info("✓ Fetched {$stored} XSMN results from RSS: {$this->province->name}");
        }
    }
}
