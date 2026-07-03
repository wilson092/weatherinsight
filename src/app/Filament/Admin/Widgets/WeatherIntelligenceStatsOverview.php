<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Weather\LatestWeatherSnapshotService;
use App\Services\Weather\WeatherAlertService;
use App\Services\Weather\WeatherRuleEngineService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WeatherIntelligenceStatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $latestWeather = app(LatestWeatherSnapshotService::class)->all();
        $ruleEngine = app(WeatherRuleEngineService::class);
        $alertService = app(WeatherAlertService::class);

        $averageRisk = round((float) $latestWeather->avg('risk_score'), 1);

        $activeAlerts = $latestWeather->filter(function ($weather) use ($ruleEngine, $alertService) {
            $analysis = $ruleEngine->analyze($weather);
            return !empty($alertService->fromAnalysis($analysis));
        })->count();

        return [
            Stat::make('Average Risk Score', number_format($averageRisk, 1) . '/100')
                ->description('Across latest city snapshots')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color(match (true) {
                    $averageRisk > 70 => 'danger',
                    $averageRisk > 30 => 'warning',
                    default => 'success',
                }),

            Stat::make('Active Alerts', $activeAlerts)
                ->description('Cities with current weather alerts')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($activeAlerts > 0 ? 'danger' : 'success'),
        ];
    }
}
