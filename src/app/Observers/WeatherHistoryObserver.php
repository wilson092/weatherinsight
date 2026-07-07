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
        $analysis = $this->ruleEngine->analyze($weather);

        // The observer should save the composite score analysis to the history record.
        $assessment = $analysis['assessment'] ?? null;

        if ($assessment) {
            $weather->risk_score = $assessment['score'];
            $weather->risk_level = $assessment['risk_level'];
            $weather->recommendation = $assessment['recommendation'];
            $weather->insight = $assessment['insight'];
        }
    }
}
