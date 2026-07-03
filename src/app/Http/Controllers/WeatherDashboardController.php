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
    public function index(
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
        $leaderboards = $leaderboardService->rankings();

        return view('weather.dashboard', [
            'latest' => $latest,
            'history' => $history,
            'forecast' => $forecast,
            'city' => $city,
            'riskAnalysis' => $riskAnalysis,
            'alerts' => $alerts,
            'leaderboards' => $leaderboards,
        ]);
    }

    public function comparison(Request $request)
    {
        $comparisonService = app(WeatherComparisonService::class);
        $ruleEngine = app(WeatherRuleEngineService::class);

        // Primary city
        $primaryCity = $request->get('primary_city', 'Jakarta');
        $primaryWeather = WeatherHistory::where('city', $primaryCity)
            ->latest()
            ->first();
        
        // Primary city risk analysis
        $primaryAnalysis = $primaryWeather ? $ruleEngine->analyze($primaryWeather) : null;

        // Comparison city
        $comparisonCity = $request->get('comparison_city');
        $comparisonData = $comparisonCity
            ? $comparisonService->findOrFetch($comparisonCity, auth()->id())
            : null;
        
        $comparisonWeather = $comparisonData ? data_get($comparisonData, 'weather') : null;
        $comparisonAnalysis = $comparisonWeather ? $ruleEngine->analyze($comparisonWeather) : null;

        return view('weather.comparison', [
            'primaryCity' => $primaryCity,
            'primaryWeather' => $primaryWeather,
            'primaryAnalysis' => $primaryAnalysis,
            'comparisonCity' => $comparisonCity,
            'comparisonWeather' => $comparisonWeather,
            'comparisonAnalysis' => $comparisonAnalysis,
            'comparisonError' => data_get($comparisonData, 'error'),
        ]);
    }

    public function leaderboard(WeatherLeaderboardService $leaderboardService)
    {
        $leaderboards = $leaderboardService->rankings();

        return view('weather.leaderboard', compact('leaderboards'));
    }

    public function history(Request $request)
    {
        $city = $request->get('city', 'Jakarta');
        $date = $request->get('date');

        $query = WeatherHistory::where('city', $city)
            ->orderBy('recorded_at', 'desc');

        if ($date) {
            $query->whereDate('recorded_at', $date);
        }

        $history = $query->paginate(20);

        return view('weather.history', compact('history', 'city'));
    }
}
