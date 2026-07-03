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

        $weather->risk_score = $analysis['score'];
        $weather->risk_level = $analysis['risk_level'];
        $weather->recommendation = $analysis['recommendation'];
        $weather->insight = $analysis['insight'];
    }
}
