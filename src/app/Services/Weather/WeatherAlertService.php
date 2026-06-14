<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;
use Illuminate\Support\Collection;

class WeatherAlertService
{
    public function forWeather(?WeatherHistory $weather): array
    {
        if (! $weather) {
            return [];
        }

        $alerts = [];
        $thresholds = config('weather-risk.thresholds');
        $weatherMain = strtolower((string) $weather->weather_main);

        if (in_array($weatherMain, ['rain', 'thunderstorm'], true)) {
            $alerts[] = [
                'key' => 'heavy-rain',
                'title' => 'Heavy Rain Risk',
                'message' => 'Rain activity may reduce visibility and disrupt outdoor travel.',
                'level' => $weatherMain === 'thunderstorm' ? 'HIGH' : 'MEDIUM',
                'icon' => 'heroicon-o-cloud-arrow-down',
            ];
        }

        if ($weather->temperature > $thresholds['temperature']) {
            $alerts[] = [
                'key' => 'extreme-heat',
                'title' => 'Extreme Heat',
                'message' => 'Limit prolonged outdoor activity and maintain hydration.',
                'level' => 'HIGH',
                'icon' => 'heroicon-o-sun',
            ];
        }

        if ($weather->wind_speed > $thresholds['wind_speed']) {
            $alerts[] = [
                'key' => 'strong-wind',
                'title' => 'Strong Wind',
                'message' => 'Secure loose objects and use caution in exposed areas.',
                'level' => 'HIGH',
                'icon' => 'heroicon-o-bars-3-bottom-left',
            ];
        }

        if ($weather->humidity > $thresholds['high_humidity_alert']) {
            $alerts[] = [
                'key' => 'high-humidity',
                'title' => 'High Humidity',
                'message' => 'High moisture levels may increase heat discomfort.',
                'level' => 'MEDIUM',
                'icon' => 'heroicon-o-beaker',
            ];
        }

        return $alerts;
    }

    public function activeCityCount(Collection $latestWeather): int
    {
        return $latestWeather
            ->filter(fn (WeatherHistory $weather): bool => $this->forWeather($weather) !== [])
            ->count();
    }
}
