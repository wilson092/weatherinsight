<?php

namespace App\Services\Weather;

use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use Illuminate\Support\Facades\Cache;
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

        // If a recent record exists and it's not older than 5 minutes, use it.
        if ($recent && $recent->recorded_at->gt(now()->subMinutes(5))) {
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

        // We should use the composite score ('assessment') for the initial record.
        $assessment = $analysis['assessment'] ?? null;

        if ($assessment) {
            $weather->update([
                'risk_score' => $assessment['score'],
                'risk_level' => $assessment['risk_level'],
                'recommendation' => $assessment['recommendation'],
                'insight' => $assessment['insight'],
            ]);
        }
        
        $freshWeather = $weather->fresh();

        // Manually update the cache with the newly fetched data.
        $cacheKey = "weather_snapshot_" . str_replace(' ', '_', strtolower($city));
        Cache::put($cacheKey, $freshWeather, now()->addMinutes(5));

        return $freshWeather;
    }
}