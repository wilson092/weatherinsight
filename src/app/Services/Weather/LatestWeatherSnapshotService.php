<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class LatestWeatherSnapshotService
{
    public function all(): Collection
    {
        return WeatherHistory::query()
            ->whereNotExists(function (Builder $query): void {
                $query->selectRaw('1')
                    ->from('weather_histories as newer_weather')
                    ->whereColumn('newer_weather.city', 'weather_histories.city')
                    ->where(function (Builder $newer): void {
                        $newer->whereColumn('newer_weather.recorded_at', '>', 'weather_histories.recorded_at')
                            ->orWhere(function (Builder $sameTime): void {
                                $sameTime->whereColumn('newer_weather.recorded_at', 'weather_histories.recorded_at')
                                    ->whereColumn('newer_weather.id', '>', 'weather_histories.id');
                            });
                    });
            })
            ->orderBy('city')
            ->get();
    }

    public function forCity(string $city): ?WeatherHistory
    {
        return WeatherHistory::query()
            ->where('city', $city)
            ->latest('recorded_at')
            ->latest('id')
            ->first();
    }
}
