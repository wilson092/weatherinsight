<?php

namespace App\Services\Weather;

use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use Throwable;

class WeatherComparisonService
{
    public function __construct(
        private readonly OpenWeatherService $openWeather,
        private readonly WeatherRuleEngineService $ruleEngine,
        private readonly LatestWeatherSnapshotService $snapshots,
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

        $existing = $this->snapshots->forCity($city);

        if ($existing) {
            return [
                'weather' => $existing,
                'source' => 'history',
                'error' => null,
            ];
        }

        try {
            $data = $this->openWeather->current($city);
        } catch (Throwable) {
            return $this->failure($city, 'Unable to reach OpenWeather for this city.');
        }

        if (! isset($data['main'], $data['wind'], $data['weather'][0])) {
            return $this->failure($city, 'Weather data was not found for this city.');
        }

        $trackedCity = TrackedCity::firstOrCreate(['city' => $city]);

        $weather = WeatherHistory::create([
            'tracked_city_id' => $trackedCity->id,
            'user_id' => $userId,
            'city' => $city,
            'latitude' => data_get($data, 'coord.lat'),
            'longitude' => data_get($data, 'coord.lon'),
            'timezone' => data_get($data, 'timezone'),
            'country' => data_get($data, 'sys.country'),
            'temperature' => $data['main']['temp'],
            'humidity' => $data['main']['humidity'],
            'pressure' => $data['main']['pressure'],
            'wind_speed' => $data['wind']['speed'],
            'weather_main' => $data['weather'][0]['main'],
            'weather_description' => $data['weather'][0]['description'],
            'weather_icon' => $data['weather'][0]['icon'],
            'recorded_at' => now(),
        ]);

        $analysis = $this->ruleEngine->analyze($weather);

        $weather->update([
            'recommendation' => $analysis['recommendation'],
            'insight' => $analysis['insight'],
            'risk_level' => $analysis['risk'],
        ]);

        return [
            'weather' => $weather->fresh(),
            'source' => 'realtime',
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
}
