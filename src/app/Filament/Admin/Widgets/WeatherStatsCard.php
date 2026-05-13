<?php

namespace App\Filament\Admin\Widgets;

use App\Models\WeatherHistory;
use Filament\Widgets\Widget;

class WeatherStatsCard extends Widget
{
    protected static string $view = 'filament.admin.widgets.weather-stats-card';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'latest' => WeatherHistory::latest()->first(),
        ];
    }
}