<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\User;
use App\Services\PredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function __construct(
        protected PredictionService $predictionService
    ) {}

    /**
     * Display all predictions from all regions.
     */
    public function indexAll()
    {
        $predictions = Prediction::published()
            ->latest()
            ->paginate(12);

        // Get admin author for display
        $author = User::where('is_admin', true)->first();

        // Get latest prediction for each region
        $latestByRegion = [];
        foreach (Prediction::REGIONS as $region => $name) {
            $latestByRegion[$region] = Prediction::forRegion($region)
                ->published()
                ->latest()
                ->first();
        }

        return view('predictions.index-all', compact(
            'predictions',
            'author',
            'latestByRegion'
        ));
    }

    /**
     * Display list of XSMB predictions.
     */
    public function indexXsmb()
    {
        return $this->index(Prediction::REGION_NORTH);
    }

    /**
     * Display list of XSMT predictions.
     */
    public function indexXsmt()
    {
        return $this->index(Prediction::REGION_CENTRAL);
    }

    /**
     * Display list of XSMN predictions.
     */
    public function indexXsmn()
    {
        return $this->index(Prediction::REGION_SOUTH);
    }

    /**
     * Display single XSMB prediction.
     */
    public function showXsmb(string $date, string $date2)
    {
        return $this->show(Prediction::REGION_NORTH, $date);
    }

    /**
     * Display single XSMT prediction.
     */
    public function showXsmt(string $date, string $date2)
    {
        return $this->show(Prediction::REGION_CENTRAL, $date);
    }

    /**
     * Display single XSMN prediction.
     */
    public function showXsmn(string $date, string $date2)
    {
        return $this->show(Prediction::REGION_SOUTH, $date);
    }

    /**
     * Display paginated list of predictions for a region.
     */
    protected function index(string $region)
    {
        $predictions = Prediction::forRegion($region)
            ->published()
            ->latest()
            ->paginate(12);

        $regionSlug = Prediction::REGION_SLUGS[$region];
        $regionName = Prediction::REGIONS[$region];

        // Get admin author for display
        $author = User::where('is_admin', true)->first();

        // Get related predictions from other regions
        $relatedPredictions = $this->getRelatedPredictions($region);

        return view('predictions.index', compact(
            'predictions',
            'region',
            'regionSlug',
            'regionName',
            'author',
            'relatedPredictions'
        ));
    }

    /**
     * Display a single prediction detail page.
     */
    protected function show(string $region, string $date)
    {
        // Parse date from URL format (dd-mm-yyyy)
        try {
            $predictionDate = Carbon::createFromFormat('d-m-Y', $date);
        } catch (\Exception $e) {
            abort(404);
        }

        $prediction = Prediction::getByRegionAndDate($region, $predictionDate->format('Y-m-d'));

        if (!$prediction) {
            abort(404);
        }

        $regionSlug = Prediction::REGION_SLUGS[$region];
        $regionName = Prediction::REGIONS[$region];

        // Get admin author for display
        $author = User::where('is_admin', true)->first();

        // Get last 10 and 30 special prizes for statistics display
        $last10SpecialPrizes = $this->predictionService->getLast10SpecialPrizes($region);
        $last30SpecialPrizes = $this->predictionService->getLast30SpecialPrizes($region);

        // Get related predictions from other regions (same date)
        $relatedPredictions = $this->getRelatedPredictions($region, $predictionDate);

        // Previous and next predictions
        $previousPrediction = Prediction::forRegion($region)
            ->published()
            ->where('prediction_date', '<', $predictionDate)
            ->orderBy('prediction_date', 'desc')
            ->first();

        $nextPrediction = Prediction::forRegion($region)
            ->published()
            ->where('prediction_date', '>', $predictionDate)
            ->orderBy('prediction_date', 'asc')
            ->first();

        // Trial draw URL
        $trialDrawUrls = [
            'north' => route('trial.xsmb'),
            'central' => route('trial.xsmt'),
            'south' => route('trial.xsmn'),
        ];
        $trialDrawUrl = $trialDrawUrls[$region] ?? route('trial.draw');

        // For XSMN/XSMT, get provinces from lottery_results_snapshot (reference date)
        $provinces = collect();
        if ($region === Prediction::REGION_SOUTH || $region === Prediction::REGION_CENTRAL) {
            // Extract provinces from lottery results snapshot
            $provinceIds = collect($prediction->lottery_results_snapshot)->pluck('province_id')->filter()->unique();
            if ($provinceIds->isNotEmpty()) {
                $provinces = \App\Models\Province::whereIn('id', $provinceIds)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }
        }

        return view('predictions.show', compact(
            'prediction',
            'region',
            'regionSlug',
            'regionName',
            'author',
            'last10SpecialPrizes',
            'last30SpecialPrizes',
            'relatedPredictions',
            'previousPrediction',
            'nextPrediction',
            'trialDrawUrl',
            'provinces'
        ));
    }

    /**
     * Get related predictions from other regions.
     */
    protected function getRelatedPredictions(string $currentRegion, ?Carbon $date = null): array
    {
        $date = $date ?? Carbon::today();
        $regions = [
            Prediction::REGION_NORTH,
            Prediction::REGION_CENTRAL,
            Prediction::REGION_SOUTH,
        ];

        $related = [];
        foreach ($regions as $region) {
            if ($region === $currentRegion) {
                continue;
            }

            $prediction = Prediction::forRegion($region)
                ->published()
                ->where('prediction_date', '<=', $date)
                ->orderBy('prediction_date', 'desc')
                ->first();

            if ($prediction) {
                $related[] = $prediction;
            }
        }

        return $related;
    }
}
