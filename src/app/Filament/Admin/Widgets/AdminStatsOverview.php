<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ApiLog;
use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use App\Models\WeatherRule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Tracked Cities',
                TrackedCity::count()
            )
                ->description('Cities monitored')
                ->descriptionIcon('heroicon-m-map-pin'),

            Stat::make(
                'Weather Records',
                WeatherHistory::count()
            )
                ->description('Stored weather history')
                ->descriptionIcon('heroicon-m-cloud'),

            Stat::make(
                'API Requests',
                ApiLog::count()
            )
                ->description('OpenWeather API logs')
                ->descriptionIcon('heroicon-m-signal'),

            Stat::make(
                'Active Rules',
                WeatherRule::where('is_active', true)->count()
            )
                ->description('Weather automation rules')
                ->descriptionIcon('heroicon-m-shield-check'),

        ];
    }
}