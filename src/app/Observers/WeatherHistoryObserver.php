<?php

namespace App\Observers;

use App\Models\WeatherHistory;
use App\Services\Weather\WeatherRiskAnalysisService;

class WeatherHistoryObserver
{
    public function __construct(
        private readonly WeatherRiskAnalysisService $riskAnalysis,
    ) {}

    public function saving(WeatherHistory $weather): void
    {
        $analysis = $this->riskAnalysis->analyze($weather);

        $weather->risk_score = $analysis['score'];
        $weather->risk_level = $analysis['level'];
    }
}
