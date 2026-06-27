<?php

namespace App\Http\Controllers;

use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use App\Services\Weather\OpenWeatherService;
use App\Services\Weather\WeatherAlertService;
use App\Services\Weather\WeatherComparisonService;
use App\Services\Weather\WeatherLeaderboardService;
use App\Services\Weather\WeatherRuleEngineService;
use Illuminate\Http\Request;

class WeatherDashboardController extends Controller
{
    public function __invoke(
        Request $request,
        OpenWeatherService $service,
        WeatherRuleEngineService $ruleEngine,
        WeatherAlertService $alertService,
        WeatherComparisonService $comparisonService,
        WeatherLeaderboardService $leaderboardService,
    ) {
        $city = $request->get('city', 'Jakarta');

        $trackedCity = TrackedCity::firstOrCreate([
            'city' => $city,
        ]);

        // FETCH CURRENT WEATHER
        $data = $service->current($city);

        // FETCH FORECAST
        $forecast = $service->forecast($city);

        // VALIDASI RESPONSE
        if (isset($data['main'])) {

            // SIMPAN WEATHER
            $weather = WeatherHistory::create([
                'tracked_city_id' => $trackedCity->id,
                'user_id' => auth()->id(),
                'city' => $city,
                'latitude' => data_get($data, 'coord.lat'),
                'longitude' => data_get($data, 'coord.lon'),
                'timezone' => data_get($data, 'timezone'),
                'country' => data_get($data, 'sys.country'),

                'temperature' => $data['main']['temp'],
                'humidity' => $data['main']['humidity'],
                'pressure' => $data['main']['pressure'],
                'wind_speed' => $data['wind']['speed'],

                'weather_main' => $data['weather'][0]['main'],
                'weather_description' => $data['weather'][0]['description'],
                'weather_icon' => $data['weather'][0]['icon'],

                'recorded_at' => now(),
            ]);

            // ANALISIS
            $analysis = $ruleEngine->analyze($weather);

            // UPDATE ANALISIS
            $weather->update([
                'recommendation' => $analysis['recommendation'],
                'insight' => $analysis['insight'],
                'risk_level' => $analysis['risk'],
            ]);
        }

        // DATA TERBARU
        $latest = WeatherHistory::where('city', $city)
            ->latest()
            ->first();

        // HISTORY
        $history = WeatherHistory::where('city', $city)
            ->latest()
            ->take(10)
            ->get()
            ->reverse();

        // WEATHER INTELLIGENCE LAYER
        $riskAnalysis = $latest ? $ruleEngine->analyze($latest) : null;
        $alerts = $latest ? $alertService->forWeather($latest) : [];
        $comparison = $comparisonService->findOrFetch(
            $request->get('compare_city'),
            auth()->id(),
        );
        $leaderboards = $leaderboardService->rankings();

        return view('weather.dashboard', [
            'latest' => $latest,
            'history' => $history,
            'forecast' => $forecast,
            'city' => $city,
            'riskAnalysis' => $riskAnalysis,
            'alerts' => $alerts,
            'comparison' => $comparison,
            'leaderboards' => $leaderboards,
        ]);
    }
}
