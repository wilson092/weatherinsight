<?php

namespace App\Services\Weather;

class WeatherImageService
{
    public static function getImageUrl(string $mainCondition, string $description): string
    {
        $basePath = '/images/weather-bg/';
        $conditionMap = [
            'Thunderstorm' => 'thunderstorm.jpg',
            'Drizzle' => 'rain.jpg',
            'Rain' => 'rain.jpg',
            'Snow' => 'snow.jpg',
            'Mist' => 'fog.jpg',
            'Smoke' => 'fog.jpg',
            'Haze' => 'fog.jpg',
            'Dust' => 'fog.jpg',
            'Fog' => 'fog.jpg',
            'Sand' => 'fog.jpg',
            'Ash' => 'fog.jpg',
            'Squall' => 'thunderstorm.jpg',
            'Tornado' => 'thunderstorm.jpg',
            'Clear' => 'clear.jpg',
            'Clouds' => 'clouds.jpg',
        ];

        $image = $conditionMap[$mainCondition] ?? 'default.jpg';

        // More specific conditions for clouds
        if ($mainCondition === 'Clouds') {
            if (str_contains(strtolower($description), 'few clouds')) {
                $image = 'few-clouds.jpg';
            } elseif (str_contains(strtolower($description), 'scattered clouds')) {
                $image = 'scattered-clouds.jpg';
            } elseif (str_contains(strtolower($description), 'broken clouds')) {
                $image = 'broken-clouds.jpg';
            } elseif (str_contains(strtolower($description), 'overcast clouds')) {
                $image = 'overcast-clouds.jpg';
            }
        }
        
        return asset($basePath . $image);
    }
}