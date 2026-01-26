<?php

namespace App\Console\Commands;

use App\Services\VietlottDataService;
use Illuminate\Console\Command;

class FetchVietlottResultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vietlott:fetch
        {--game= : Specific game to fetch (mega645, power655, max3d, max3dpro)}
        {--force : Force re-sync all data regardless of existing records}
        {--fill-gaps : Detect and fill missing dates based on draw schedule}
        {--days=30 : Days to look back when filling gaps}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch Vietlott lottery results from GitHub data repository';

    /**
     * Execute the console command.
     */
    public function handle(VietlottDataService $service): int
    {
        $game = $this->option('game');
        $force = $this->option('force');
        $fillGaps = (bool) $this->option('fill-gaps');
        $daysBack = (int) $this->option('days');

        if ($fillGaps) {
            return $game
                ? $this->fillGapsSingleGame($service, $game, $daysBack)
                : $this->fillGapsAllGames($service, $daysBack);
        }

        if ($game) {
            return $this->fetchSingleGame($service, $game, $force);
        }

        return $this->fetchAllGames($service, $force);
    }

    /**
     * Fetch results for a single game
     */
    private function fetchSingleGame(VietlottDataService $service, string $game, bool $force): int
    {
        $validGames = VietlottDataService::getGameTypes();

        if (!in_array($game, $validGames)) {
            $this->error("Invalid game type: {$game}");
            $this->info("Available games: " . implode(', ', $validGames));
            return 1;
        }

        $gameInfo = VietlottDataService::getGameInfo($game);
        $this->info("Fetching results for: {$gameInfo['name']} ({$game})" . ($force ? ' [FORCE]' : ''));

        try {
            $stats = $service->syncGame($game, $force);

            $this->info("Results: {$stats['new']} new, {$stats['skipped']} skipped" .
                ($stats['errors'] > 0 ? ", {$stats['errors']} errors" : ''));

            return $stats['errors'] > 0 && $stats['new'] === 0 ? 1 : 0;

        } catch (\Exception $e) {
            $this->error("Failed to fetch {$gameInfo['name']}: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Fetch results for all games
     */
    private function fetchAllGames(VietlottDataService $service, bool $force): int
    {
        $this->info('Fetching Vietlott results for all games...' . ($force ? ' [FORCE]' : ''));
        $this->newLine();

        try {
            $results = $service->syncAllGames($force);

            // Display results table
            $tableData = [];
            $totalNew = 0;
            $totalSkipped = 0;
            $totalErrors = 0;
            $hasFailures = false;

            foreach ($results as $gameType => $stats) {
                $gameInfo = VietlottDataService::getGameInfo($gameType);

                $status = isset($stats['error_message'])
                    ? 'Failed'
                    : ($stats['new'] > 0 ? 'Success' : 'Up to date');

                $tableData[] = [
                    $gameInfo['name'],
                    $gameType,
                    $stats['new'],
                    $stats['skipped'],
                    $stats['errors'],
                    $status,
                ];

                $totalNew += $stats['new'];
                $totalSkipped += $stats['skipped'];
                $totalErrors += $stats['errors'];

                if (isset($stats['error_message'])) {
                    $hasFailures = true;
                }
            }

            $this->table(
                ['Game', 'Code', 'New', 'Skipped', 'Errors', 'Status'],
                $tableData
            );

            $this->newLine();
            $this->info("Summary:");
            $this->info("  - New results: {$totalNew}");
            $this->info("  - Skipped (existing): {$totalSkipped}");
            if ($totalErrors > 0) {
                $this->warn("  - Errors: {$totalErrors}");
            }

            return $hasFailures ? 1 : 0;

        } catch (\Exception $e) {
            $this->error("Failed to fetch Vietlott results: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Fill gaps for a single game
     */
    private function fillGapsSingleGame(VietlottDataService $service, string $game, int $daysBack): int
    {
        $validGames = VietlottDataService::getGameTypes();

        if (!in_array($game, $validGames)) {
            $this->error("Invalid game type: {$game}");
            $this->info("Available games: " . implode(', ', $validGames));
            return 1;
        }

        $gameInfo = VietlottDataService::getGameInfo($game);
        $this->info("Filling gaps for: {$gameInfo['name']} ({$game}) - looking back {$daysBack} days");

        try {
            $stats = $service->syncMissingResults($game, $daysBack);

            $this->info("Results: {$stats['new']} new, {$stats['skipped']} skipped" .
                ($stats['errors'] > 0 ? ", {$stats['errors']} errors" : ''));

            return $stats['errors'] > 0 && $stats['new'] === 0 ? 1 : 0;

        } catch (\Exception $e) {
            $this->error("Failed to fill gaps for {$gameInfo['name']}: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Fill gaps for all games
     */
    private function fillGapsAllGames(VietlottDataService $service, int $daysBack): int
    {
        $this->info("Filling gaps for all Vietlott games - looking back {$daysBack} days...");
        $this->newLine();

        try {
            $results = $service->syncAllMissingResults($daysBack);

            // Display results table
            $tableData = [];
            $totalNew = 0;
            $totalSkipped = 0;
            $totalErrors = 0;
            $hasFailures = false;

            foreach ($results as $gameType => $stats) {
                $gameInfo = VietlottDataService::getGameInfo($gameType);

                $status = isset($stats['error_message'])
                    ? 'Failed'
                    : ($stats['new'] > 0 ? 'Filled' : 'No gaps');

                $tableData[] = [
                    $gameInfo['name'],
                    $gameType,
                    $stats['new'],
                    $stats['skipped'],
                    $stats['errors'],
                    $status,
                ];

                $totalNew += $stats['new'];
                $totalSkipped += $stats['skipped'];
                $totalErrors += $stats['errors'];

                if (isset($stats['error_message'])) {
                    $hasFailures = true;
                }
            }

            $this->table(
                ['Game', 'Code', 'Filled', 'Skipped', 'Errors', 'Status'],
                $tableData
            );

            $this->newLine();
            $this->info("Summary:");
            $this->info("  - Gaps filled: {$totalNew}");
            $this->info("  - Skipped (existing): {$totalSkipped}");
            if ($totalErrors > 0) {
                $this->warn("  - Errors: {$totalErrors}");
            }

            return $hasFailures ? 1 : 0;

        } catch (\Exception $e) {
            $this->error("Failed to fill gaps: " . $e->getMessage());
            return 1;
        }
    }
}
