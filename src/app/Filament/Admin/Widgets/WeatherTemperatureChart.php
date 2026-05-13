<?php

namespace App\Filament\Admin\Widgets;

use App\Models\WeatherHistory;
use Filament\Widgets\ChartWidget;

class WeatherTemperatureChart extends ChartWidget
{
    protected static ?string $heading = 'Temperature Trend';

    public ?string $filter = 'Jakarta';

    protected function getFilters(): ?array
    {
        return WeatherHistory::query()
            ->distinct()
            ->pluck('city', 'city')
            ->toArray();
    }

    protected function getData(): array
    {
        $data = WeatherHistory::query()
            ->where('city', $this->filter)
            ->latest()
            ->take(10)
            ->get()
            ->reverse();

        return [
            'datasets' => [
                [
                    'label' => 'Temperature',
                    'data' => $data->pluck('temperature')->toArray(),
                ],
            ],

            'labels' => $data
                ->pluck('recorded_at')
                ->map(fn ($date) => $date->format('H:i'))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}