<?php

namespace App\Services\Weather;

use Illuminate\Support\Arr;

class WeatherAlertService
{
    /**
     * Generate weather alerts from a risk analysis result.
     *
     * @param array|null $riskAnalysis The result from WeatherRuleEngineService::analyze()
     * @return array
     */
    public function fromAnalysis(?array $riskAnalysis): array
    {
        if (! $riskAnalysis || Arr::get($riskAnalysis, 'risk_score', 0) <= 0) {
            return [];
        }

        $category = Arr::get($riskAnalysis, 'risk_category');

        if (! $category) {
            return [];
        }

        $activeRulesCount = count(Arr::get($riskAnalysis, 'triggered_rules', []));

        return [
            [
                'key' => $category->level,
                'title' => $category->name,
                'message' => $category->description,
                'level' => $category->severity,
                'icon' => $category->icon,
                'count' => $activeRulesCount,
            ],
        ];
    }
}