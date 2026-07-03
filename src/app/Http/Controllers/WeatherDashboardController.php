<?php

namespace App\Http\Controllers;

use App\Models\TrackedCity;
use App\Models\WeatherHistory;
use App\Services\Weather\OpenWeatherService;
use App\Services\Weather\WeatherAlertService;
use App\Services\Weather\WeatherComparisonService;
use App\Services\Weather\WeatherLeaderboardService;
use App\Services\Weather\WeatherRuleEngineService;
use App\Services\Weather\WeatherSnapshotService;
use Illuminate\Http\Request;

class WeatherDashboardController extends Controller
{
    public function index(
        Request $request,
        WeatherRuleEngineService $ruleEngine,
        WeatherAlertService $alertService,
        WeatherLeaderboardService $leaderboardService,
        WeatherSnapshotService $snapshotService,
        OpenWeatherService $openWeather, // Keep for forecast
    ) {
        $city = $request->get('city', 'Jakarta');

        // Get latest weather data using the centralized service
        $latest = $snapshotService->getLatest($city, auth()->id());

        // Fetch forecast (still separate for now)
        $forecast = $openWeather->forecast($city);

        // HISTORY
        $history = WeatherHistory::where('city', $city)
            ->latest()
            ->take(10)
            ->get()
            ->reverse();

        // WEATHER INTELLIGENCE LAYER
        $riskAnalysis = $latest ? $ruleEngine->analyze($latest) : null;
        $alerts = $alertService->fromAnalysis($riskAnalysis);
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

        // Generate Comparison Summary
        $summary = $comparisonService->generateComparisonSummary(
            $primaryAnalysis,
            $comparisonAnalysis,
            $primaryWeather,
            $comparisonWeather
        );

        return view('weather.comparison', [
            'primaryCity' => $primaryCity,
            'primaryWeather' => $primaryWeather,
            'primaryAnalysis' => $primaryAnalysis,
            'comparisonCity' => $comparisonCity,
            'comparisonWeather' => $comparisonWeather,
            'comparisonAnalysis' => $comparisonAnalysis,
            'comparisonError' => data_get($comparisonData, 'error'),
            'summary' => $summary,
        ]);
    }

    public function leaderboard(WeatherLeaderboardService $leaderboardService)
    {
        $leaderboards = $leaderboardService->rankings();

        return view('weather.leaderboard', compact('leaderboards'));
    }

    public function history(Request $request)
    {
        $date = $request->get('date');

        $query = WeatherHistory::query()
            ->orderBy('recorded_at', 'desc');

        if ($date) {
            $query->whereDate('recorded_at', $date);
        }

        $history = $query->paginate(20)->appends($request->only('date'));
        $city = null;

        return view('weather.history', compact('history', 'city'));
    }
}
