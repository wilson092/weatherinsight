<?php

namespace App\Services\Weather;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ForecastParserService
{
    /**
     * Parse the raw forecast API response into structured daily and hourly forecasts.
     *
     * @param array $apiResponse The raw response from OpenWeather's /forecast endpoint.
     * @param int $timezoneOffset The timezone offset in seconds from UTC.
     * @return array An array containing 'daily' and 'hourly' forecasts.
     */
    public function parse(array $apiResponse, int $timezoneOffset = 0): array
    {
        $list = data_get($apiResponse, 'list', []);

        if (empty($list)) {
            return [
                'daily' => [],
                'hourly' => [],
            ];
        }

        return [
            'daily' => $this->parseDailyForecast($list, $timezoneOffset),
            'hourly' => $this->parseHourlyForecast($list, $timezoneOffset),
        ];
    }

    /**
     * Extracts a rolling 24-hour forecast, adjusted for the local timezone.
     *
     * @param array $list The 'list' array from the API response.
     * @param int $timezoneOffset The timezone offset in seconds.
     * @return array The extracted hourly forecast data.
     */
    private function parseHourlyForecast(array $list, int $timezoneOffset): array
    {
        $now = Carbon::now()->timestamp;

        return collect($list)
            // Filter for items that are in the future
            ->filter(fn ($item) => $item['dt'] >= $now)
            // Add the local timestamp
            ->map(function ($item) use ($timezoneOffset) {
                $item['local_dt'] = $item['dt'] + $timezoneOffset;
                return $item;
            })
            // Take the first 8 items for the next 24 hours
            ->take(8)
            ->values()
            ->all();
    }

    /**
     * Groups the 5-day forecast data by day, adjusted for local timezone.
     *
     * @param array $list The 'list' array from the API response.
     * @param int $timezoneOffset The timezone offset in seconds.
     * @return array The structured daily forecast data.
     */
    private function parseDailyForecast(array $list, int $timezoneOffset): array
    {
        return collect($list)
            ->groupBy(function ($item) use ($timezoneOffset) {
                // Group by the local date
                return Carbon::createFromTimestamp($item['dt'] + $timezoneOffset)->format('Y-m-d');
            })
            ->map(function (Collection $dayItems) use ($timezoneOffset) {
                // Use the item around midday (local time) for the representative weather icon
                $representativeItem = $dayItems->first(function ($item) use ($timezoneOffset) {
                    $localHour = Carbon::createFromTimestamp($item['dt'] + $timezoneOffset)->hour;
                    return $localHour >= 11 && $localHour <= 13;
                }) ?? $dayItems->first();

                // Calculate min and max temperature for the entire day
                $minTempOfDay = $dayItems->min('main.temp');
                $maxTempOfDay = $dayItems->max('main.temp');

                return [
                    'dt' => Carbon::createFromTimestamp($dayItems->first()['dt'] + $timezoneOffset)->startOfDay()->timestamp,
                    'temp' => [
                        'min' => $minTempOfDay,
                        'max' => $maxTempOfDay,
                        'current' => data_get($representativeItem, 'main.temp'), // Representative temp for the day tab
                    ],
                    'weather' => data_get($representativeItem, 'weather'),
                    'humidity' => data_get($representativeItem, 'main.humidity'),
                    'pressure' => data_get($representativeItem, 'main.pressure'),
                    'wind_speed' => data_get($representativeItem, 'wind.speed'),
                    'pop' => data_get($representativeItem, 'pop'), // Probability of precipitation
                ];
            })
            ->values()
            ->all();
    }
}