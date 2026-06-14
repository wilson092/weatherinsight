<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;

class WeatherRiskAnalysisService
{
    public function analyze(WeatherHistory|array $weather): array
    {
        $points = (int) config('weather-risk.points_per_condition', 25);
        $thresholds = config('weather-risk.thresholds');

        $conditions = [
            'temperature' => $this->value($weather, 'temperature') > $thresholds['temperature'],
            'humidity' => $this->value($weather, 'humidity') > $thresholds['humidity'],
            'wind_speed' => $this->value($weather, 'wind_speed') > $thresholds['wind_speed'],
            'pressure' => $this->value($weather, 'pressure') < $thresholds['pressure_min']
                || $this->value($weather, 'pressure') > $thresholds['pressure_max'],
        ];

        $score = collect($conditions)->filter()->count() * $points;

        return [
            'score' => $score,
            'level' => $this->level($score),
            'conditions' => $conditions,
        ];
    }

    public function level(int $score): string
    {
        return match (true) {
            $score <= 30 => 'LOW',
            $score <= 70 => 'MEDIUM',
            default => 'HIGH',
        };
    }

    private function value(WeatherHistory|array $weather, string $key): float
    {
        return (float) ($weather instanceof WeatherHistory ? $weather->getAttribute($key) : ($weather[$key] ?? 0));
    }
}
