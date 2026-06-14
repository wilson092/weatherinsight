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
}
