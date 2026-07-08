<?php

namespace App\Services\Weather;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenWeatherService
{
    public function current(string $city = 'Jakarta'): array
    {
        $cacheKey = "weather:current:{$city}";
        $cacheDuration = now()->addMinutes(10);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($city) {
            $response = Http::get(
                'https://api.openweathermap.org/data/2.5/weather',
                [
                    'q' => $city,
                    'appid' => env('OPENWEATHER_API_KEY'),
                    'units' => 'metric',
                ]
            );

            ApiLog::create([
                'city' => $city,
                'endpoint' => 'OpenWeather Current API',
                'status_code' => $response->status(),
                'status' => $response->successful() ? 'success' : 'failed',
            ]);

            return $response->json();
        });
    }

    public function forecast(string $city = 'Jakarta'): array
    {
        $cacheKey = "weather:forecast:{$city}";
        $cacheDuration = now()->addMinutes(10);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($city) {
            $response = Http::get(
                'https://api.openweathermap.org/data/2.5/forecast',
                [
                    'q' => $city,
                    'appid' => env('OPENWEATHER_API_KEY'),
                    'units' => 'metric',
                ]
            );

            ApiLog::create([
                'city' => $city,
                'endpoint' => 'OpenWeather Forecast API',
                'status_code' => $response->status(),
                'status' => $response->successful() ? 'success' : 'failed',
            ]);

            return $response->json();
        });
    }
}
