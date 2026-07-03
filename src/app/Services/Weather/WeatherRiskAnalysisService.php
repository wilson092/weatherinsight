<?php

namespace App\Services\Weather;

use App\Models\RiskCategory;
use App\Models\WeatherHistory;

class WeatherRiskAnalysisService
{
    public function __construct(
        private readonly WeatherRuleEngineService $ruleEngine,
    ) {}

    public function analyze(WeatherHistory|array $weather): array
    {
        $weatherModel = $weather instanceof WeatherHistory ? $weather : new WeatherHistory($weather);
        $analysis = $this->ruleEngine->analyze($weatherModel);

        return [
            'score' => $analysis['score'],
            'level' => strtoupper($analysis['risk_level'] ?? 'LOW'),
            'risk' => $analysis['risk'] ?? ($analysis['risk_category']->name ?? 'N/A'),
            'conditions' => [],
        ];
    }

    public function level(int $score): string
    {
        return strtoupper(RiskCategory::forScore($score)?->risk_level ?? 'LOW');
    }

    private function value(WeatherHistory|array $weather, string $key): float
    {
        return (float) ($weather instanceof WeatherHistory ? $weather->getAttribute($key) : ($weather[$key] ?? 0));
    }
}
