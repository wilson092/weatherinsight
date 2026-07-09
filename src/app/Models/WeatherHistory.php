<?php

namespace App\Models;

use App\Models\RiskCategory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    //
    protected $fillable = [
        'user_id',
        'tracked_city_id',
        'city',
        'latitude',
        'longitude',
        'timezone',
        'country',
        'temperature',
        'humidity',
        'pressure',
        'wind_speed',
        'weather_main',
        'weather_description',
        'weather_icon',
        'recorded_at',
        'recommendation',
        'insight',
        'risk_level',
        'risk_score',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'timezone' => 'integer',
        'temperature' => 'float',
        'humidity' => 'float',
        'pressure' => 'float',
        'wind_speed' => 'float',
        'risk_score' => 'integer',
    ];

    public function getModalData()
    {
        return [
            'city' => $this->city,
            'recorded_at' => $this->recorded_at ? $this->recorded_at->toIso8601String() : null,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'pressure' => $this->pressure,
            'wind_speed' => $this->wind_speed,
            'risk_level' => $this->risk_level,
            'weather_description' => $this->weather_description,
            'recommendation' => $this->recommendation,
            'insight' => $this->insight,
        ];
    }

    public function getRiskCategoryAttribute(): ?RiskCategory
    {
        if (! isset($this->risk_score)) {
            return null;
        }

        return RiskCategory::forScore((int) $this->risk_score);
    }

    public static function getHottestCities(int $userId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->select('city', 'temperature', 'weather_icon', 'weather_description')
            ->orderByDesc('temperature')
            ->groupBy('city', 'temperature', 'weather_icon', 'weather_description')
            ->limit(5)
            ->get();
    }

    public static function getColdestCities(int $userId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->select('city', 'temperature', 'weather_icon', 'weather_description')
            ->orderBy('temperature')
            ->groupBy('city', 'temperature', 'weather_icon', 'weather_description')
            ->limit(5)
            ->get();
    }

    public static function getMostHumidCities(int $userId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->select('city', 'humidity')
            ->orderByDesc('humidity')
            ->groupBy('city', 'humidity')
            ->limit(5)
            ->get();
    }

    public static function getWindiestCities(int $userId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->select('city', 'wind_speed')
            ->orderByDesc('wind_speed')
            ->groupBy('city', 'wind_speed')
            ->limit(5)
            ->get();
    }
}
