<?php

namespace App\Services\Weather;

use Illuminate\Support\Facades\Http;

class OpenWeatherService
{
    public function current(string $city = 'Jakarta'): array
    {
        $response = Http::get(
            'https://api.openweathermap.org/data/2.5/weather',
            [
                'q' => $city,
                'appid' => env('OPENWEATHER_API_KEY'),
                'units' => 'metric',
            ]
        );

        return $response->json();
    }

    public function forecast(string $city = 'Jakarta'): array
    {
        $response = Http::get(
            'https://api.openweathermap.org/data/2.5/forecast',
            [
                'q' => $city,
                'appid' => env('OPENWEATHER_API_KEY'),
                'units' => 'metric',
            ]
        );

        return $response->json();
    }
}