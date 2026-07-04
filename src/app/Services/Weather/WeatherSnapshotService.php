<?php

namespace App\Services\Weather;

use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use Throwable;

class WeatherSnapshotService
{
    public function __construct(
        private readonly OpenWeatherService $openWeather,
        private readonly WeatherRuleEngineService $ruleEngine,
        private readonly LatestWeatherSnapshotService $snapshots,
    ) {}

    public function getLatest(string $city, ?int $userId = null): ?WeatherHistory
    {
        $city = trim($city);
        if ($city === '') {
            return null;
        }

        // First, try to get a very recent snapshot to avoid API calls
        $recent = $this->snapshots->forCity($city);
        if ($recent) {
            return $recent;
        }

        // If not found or stale, fetch from API
        try {
            $data = $this->openWeather->current($city);
        } catch (Throwable) {
            // If API fails, fall back to the latest record in DB, even if old
            return WeatherHistory::where('city', $city)->latest()->first();
        }

        if (! isset($data['main'], $data['wind'], $data['weather'][0])) {
            // If API returns invalid data, fall back to DB
            return WeatherHistory::where('city', $city)->latest()->first();
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
            'risk_score' => $analysis['score'],
            'risk_level' => $analysis['risk_level'],
            'recommendation' => $analysis['recommendation'],
            'insight' => $analysis['insight'],
        ]);

        return $weather->fresh();
    }
}