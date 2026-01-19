<?php

namespace App\Console\Commands;

use App\Models\Province;
use App\Services\LotteryApiService;
use Illuminate\Console\Command;

class FetchXSMBTodayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:fetch-xsmb-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch today\'s XSMB Hà Nội result from the new real-time API';

    /**
     * Execute the console command.
     */
    public function handle(LotteryApiService $service): int
    {
        $province = Province::where('code', 'miba')->first();

        if (!$province) {
            $this->error('XSMB Hà Nội province not found (code: miba)');
            return self::FAILURE;
        }

        $this->info('Fetching today\'s XSMB from new API...');

        $success = $service->fetchAndStoreXSMBToday($province);

        if ($success) {
            $this->info('✓ Successfully fetched and stored XSMB result');
            return self::SUCCESS;
        } else {
            $this->error('✗ Failed to fetch XSMB result');
            return self::FAILURE;
        }
    }
}
