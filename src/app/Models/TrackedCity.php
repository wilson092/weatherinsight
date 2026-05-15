<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedCity extends Model
{
    protected $fillable = [
        'city',
    ];
    public function histories()
{
    return $this->hasMany(WeatherHistory::class, 'city', 'city');
}
}