<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WeatherAlertService
{
    /**
     * Generate weather alerts from a risk analysis result.
     *
     * @param array|null $riskAnalysis The result from WeatherRuleEngineService::analyze()
     * @return array
     */
    public function forWeather(WeatherHistory $weather): array
    {
        $analysis = app(WeatherRuleEngineService::class)->analyze($weather);

        return $this->fromAnalysis($analysis);
    }

    public function fromAnalysis(?array $riskAnalysis): array
    {
        if (! $riskAnalysis || Arr::get($riskAnalysis, 'score', 0) <= 0) {
            return [];
        }

        $category = Arr::get($riskAnalysis, 'risk_category');

        if (! $category) {
            return [];
        }

        $activeRulesCount = count(Arr::get($riskAnalysis, 'triggered_rules', []));
        $riskLevel = Str::lower($category->risk_level ?? 'low');

        $iconMap = [
            'high' => 'heroicon-o-fire',
            'medium' => 'heroicon-o-exclamation-triangle',
            'low' => 'heroicon-o-shield-check',
        ];

        return [
            [
                'key' => $category->risk_level,
                'title' => $category->name,
                'message' => $category->insight ?? $category->recommendation,
                'level' => ucfirst($category->risk_level),
                'icon' => $iconMap[$riskLevel] ?? 'heroicon-o-shield-check',
                'count' => $activeRulesCount,
            ],
        ];
    }
}