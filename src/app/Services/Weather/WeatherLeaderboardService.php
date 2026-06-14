<?php

namespace App\Services\Weather;

use Illuminate\Support\Collection;

class WeatherLeaderboardService
{
    public function __construct(
        private readonly LatestWeatherSnapshotService $snapshots,
    ) {}

    public function rankings(): array
    {
        $latest = $this->snapshots->all();

        return [
            'hottest' => $this->top($latest, 'temperature'),
            'humid' => $this->top($latest, 'humidity'),
            'wind' => $this->top($latest, 'wind_speed'),
            'risk' => $this->top($latest, 'risk_score'),
        ];
    }

    private function top(Collection $weather, string $field): Collection
    {
        return $weather
            ->sortByDesc(fn ($item) => $item->{$field})
            ->take(5)
            ->values();
    }
}
