<?php

namespace App\Http\Controllers;

use App\Models\WeatherHistory;
use App\Services\Weather\OpenWeatherService;
use App\Services\Weather\WeatherRuleEngineService;
use Illuminate\Http\Request;
use App\Models\TrackedCity;

class WeatherDashboardController extends Controller
{
    public function __invoke(
        Request $request,
        OpenWeatherService $service,
        WeatherRuleEngineService $ruleEngine
    ) {
        $city = $request->get('city', 'Jakarta');

        TrackedCity::firstOrCreate([
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
                'city' => $city,

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

        return view('weather.dashboard', [
            'latest' => $latest,
            'history' => $history,
            'forecast' => $forecast,
            'city' => $city,
        ]);
    }
}