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
    ];
protected $casts = [
    'recorded_at' => 'datetime',

    'temperature' => 'float',
    'humidity' => 'float',
    'pressure' => 'float',
    'wind_speed' => 'float',
];
}
