<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;
use App\Models\WeatherRule;

class WeatherRuleEngineService
{
    public function analyze(WeatherHistory $weather): array
    {
        $temp = $weather->temperature;

        $rule = WeatherRule::query()
            ->where('is_active', true)
            ->where('min_temp', '<=', $temp)
            ->where('max_temp', '>=', $temp)
            ->first();

        if ($rule) {

            return [
                'risk' => $rule->risk_level,
                'recommendation' => $rule->recommendation,
                'insight' => $rule->insight,
            ];
        }

        return [
            'risk' => 'LOW',
            'recommendation' => 'Cuaca normal',
            'insight' => 'Tidak ada risiko cuaca',
        ];
    }
}