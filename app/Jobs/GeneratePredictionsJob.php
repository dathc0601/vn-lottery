<?php

namespace App\Jobs;

use App\Models\Prediction;
use App\Services\PredictionArticleService;
use App\Services\PredictionService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePredictionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?Carbon $targetDate;
    protected ?string $targetRegion;

    /**
     * Create a new job instance.
     */
    public function __construct(?Carbon $targetDate = null, ?string $targetRegion = null)
    {
        $this->targetDate = $targetDate;
        $this->targetRegion = $targetRegion;
    }

    /**
     * Execute the job.
     */
    public function handle(PredictionService $predictionService, PredictionArticleService $articleService): void
    {
        $predictionDate = $this->targetDate ?? Carbon::today();
        $regions = $this->targetRegion
            ? [$this->targetRegion]
            : [Prediction::REGION_NORTH, Prediction::REGION_CENTRAL, Prediction::REGION_SOUTH];

        Log::info("Starting prediction generation for date: {$predictionDate->format('Y-m-d')}", [
            'regions' => $regions,
        ]);

        foreach ($regions as $region) {
            try {
                // Generate prediction
                $prediction = $predictionService->generatePrediction($region, $predictionDate);

                if (!$prediction) {
                    Log::warning("Failed to generate prediction for region: {$region}");
                    continue;
                }

                // Generate article
                $article = $articleService->generateArticle($prediction);

                if ($article) {
                    // Publish prediction
                    $predictionService->publishPrediction($prediction);
                    Log::info("Successfully generated and published prediction", [
                        'region' => $region,
                        'date' => $predictionDate->format('Y-m-d'),
                        'prediction_id' => $prediction->id,
                        'article_id' => $article->id,
                    ]);
                } else {
                    Log::warning("Failed to generate article for prediction", [
                        'prediction_id' => $prediction->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error generating prediction for region: {$region}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info("Completed prediction generation for date: {$predictionDate->format('Y-m-d')}");
    }
}
