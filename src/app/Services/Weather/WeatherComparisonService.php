<?php

namespace App\Services\Weather;

use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use Throwable;

class WeatherComparisonService
{
    public function __construct(
        private readonly WeatherSnapshotService $snapshotService,
    ) {}

    public function findOrFetch(?string $city, ?int $userId = null): ?array
    {
        $city = trim((string) $city);

        if ($city === '') {
            return null;
        }

        if (mb_strlen($city) > 255) {
            return $this->failure($city, 'City name is too long.');
        }

        $weather = $this->snapshotService->getLatest($city, $userId);

        if (! $weather) {
            return $this->failure($city, 'Weather data was not found for this city.');
        }

        return [
            'weather' => $weather,
            'source' => $weather->wasRecentlyCreated ? 'realtime' : 'history',
            'error' => null,
        ];
    }

    private function failure(string $city, string $message): array
    {
        return [
            'weather' => null,
            'source' => null,
            'city' => $city,
            'error' => $message,
        ];
    }

    public function generateComparisonSummary(?array $primaryAnalysis, ?array $comparisonAnalysis, ?WeatherHistory $primaryWeather, ?WeatherHistory $comparisonWeather): ?array
    {
        if (! $primaryAnalysis || ! $comparisonAnalysis || ! $primaryWeather || ! $comparisonWeather) {
            return null;
        }

        $primaryScore = $primaryAnalysis['score'] ?? 0;
        $comparisonScore = $comparisonAnalysis['score'] ?? 0;
        $riskDiff = $primaryScore - $comparisonScore;

        $tempDiff = $primaryWeather->temperature - $comparisonWeather->temperature;
        $humidityDiff = $primaryWeather->humidity - $comparisonWeather->humidity;
        $windDiff = $primaryWeather->wind_speed - $comparisonWeather->wind_speed;

        // Risk Summary
        $riskSummary = 'Both cities have similar risk levels.';
        if (abs($riskDiff) >= 5) {
            $saferCity = $riskDiff < 0 ? $primaryWeather->city : $comparisonWeather->city;
            $riskSummary = "<span class=\"font-semibold text-white\">{$saferCity}</span> is <span class=\"font-bold text-rose-300\">safer (lower risk score)</span>.";
        }

        // Temperature Summary
        $tempSummary = 'Both cities have similar temperatures.';
        if (abs($tempDiff) >= 1) {
            $adj = $tempDiff > 0 ? 'warmer' : 'cooler';
            $tempSummary = "<span class=\"font-semibold text-white\">{$primaryWeather->city}</span> is <span class=\"font-bold text-orange-300\">".abs(round($tempDiff, 1))."°C {$adj}</span>.";
        }

        // Humidity Summary
        $humiditySummary = 'Similar humidity levels.';
        if (abs($humidityDiff) >= 5) {
            $moreHumidCity = $humidityDiff > 0 ? $primaryWeather->city : $comparisonWeather->city;
            $humiditySummary = "<span class=\"font-semibold text-white\">{$moreHumidCity}</span> is <span class=\"font-bold text-blue-300\">".abs($humidityDiff)."% more humid</span>.";
        }

        // Wind Summary
        $windSummary = 'Similar wind speeds.';
        if (abs($windDiff) >= 0.5) {
            $windierCity = $windDiff > 0 ? $primaryWeather->city : $comparisonWeather->city;
            $windSummary = "<span class=\"font-semibold text-white\">{$windierCity}</span> has <span class=\"font-bold text-teal-300\">".abs(round($windDiff, 1))." m/s stronger winds</span>.";
        }

        return [
            'risk' => $riskSummary,
            'temperature' => $tempSummary,
            'humidity' => $humiditySummary,
            'wind' => $windSummary,
        ];
    }
}
