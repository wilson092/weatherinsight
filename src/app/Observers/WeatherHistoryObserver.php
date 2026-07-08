<?php

namespace App\Observers;

use App\Models\WeatherHistory;
use App\Services\Weather\WeatherRuleEngineService;

class WeatherHistoryObserver
{
    public function __construct(
        private readonly WeatherRuleEngineService $ruleEngine,
    ) {}

    public function saving(WeatherHistory $weather): void
    {
        // Call the main analysis to get both assessment and recommendation
        $analysis = $this->ruleEngine->analyze($weather);

        // Data for Weather Risk Assessment (Composite Score)
        $assessment = $analysis['assessment'] ?? null;
        if ($assessment) {
            $weather->risk_score = $assessment['score'];
        }

        // Data for Weather Recommendation (Temperature-based)
        // This will now be the source for the main risk level in the history table
        $recommendationAnalysis = $analysis['recommendation'] ?? null;
        if ($recommendationAnalysis) {
            $weather->risk_level = $recommendationAnalysis['risk_level'];
            $weather->recommendation = $recommendationAnalysis['recommendation'];
            $weather->insight = $recommendationAnalysis['insight'];
        }
    }
}
