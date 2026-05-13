<?php

namespace App\Filament\Admin\Widgets;

use App\Models\WeatherHistory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WeatherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $latest = WeatherHistory::latest()->first();

        if (! $latest) {
            return [];
        }

        return [
            Stat::make('Temperature', $latest->temperature . ' °C'),

            Stat::make('Humidity', $latest->humidity . ' %'),

            Stat::make('Pressure', $latest->pressure . ' hPa'),

            Stat::make('Wind Speed', $latest->wind_speed . ' km/h'),
        ];
    }
}