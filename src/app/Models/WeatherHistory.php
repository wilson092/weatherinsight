<?php

namespace App\Models;

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
}
