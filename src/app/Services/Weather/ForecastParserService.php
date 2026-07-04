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
     * @return array An array containing 'daily' and 'hourly' forecasts.
     */
    public function parse(array $apiResponse): array
    {
        $list = data_get($apiResponse, 'list', []);

        if (empty($list)) {
            return [
                'daily' => [],
                'hourly' => [],
            ];
        }

        return [
            'daily' => $this->parseDailyForecast($list),
            'hourly' => $this->parseHourlyForecast($list),
        ];
    }

    /**
     * Extracts the hourly forecast for the next 24 hours.
     *
     * @param array $list The 'list' array from the API response.
     * @return array The extracted hourly forecast data.
     */
    private function parseHourlyForecast(array $list): array
    {
        // Take the first 8 items (3-hour intervals for 24 hours)
        return array_slice($list, 0, 8);
    }

    /**
     * Groups the 5-day forecast data by day and calculates min/max temperatures.
     *
     * @param array $list The 'list' array from the API response.
     * @return array The structured daily forecast data.
     */
    private function parseDailyForecast(array $list): array
    {
        return collect($list)
            ->groupBy(function ($item) {
                return Carbon::parse($item['dt_txt'])->format('Y-m-d');
            })
            ->map(function (Collection $dayItems) {
                // Use the item around midday for the representative weather icon and description
                $representativeItem = $dayItems->first(function ($item) {
                    $hour = Carbon::parse($item['dt_txt'])->hour;
                    return $hour >= 11 && $hour <= 13;
                }) ?? $dayItems->first();

                return [
                    'dt' => Carbon::parse($dayItems->first()['dt_txt'])->timestamp,
                    'temp' => [
                        'min' => $dayItems->min('main.temp_min'),
                        'max' => $dayItems->max('main.temp_max'),
                    ],
                    'weather' => $representativeItem['weather'],
                ];
            })
            ->values()
            ->all();
    }
}