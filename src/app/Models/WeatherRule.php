<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherRule extends Model
{
    protected $fillable = [

        'name',

        'min_temp',

        'max_temp',

        'risk_level',

        'recommendation',

        'insight',

        'is_active',

    ];
}