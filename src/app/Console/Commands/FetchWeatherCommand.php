<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeatherHistory;
use App\Services\Weather\OpenWeatherService;
use App\Services\Weather\WeatherRuleEngineService;
use App\Models\TrackedCity;
// use App\Models\ApiLog;
class FetchWeatherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch realtime weather data from OpenWeather API';

    /**
     * Execute the console command.
     */
    public function handle(
    OpenWeatherService $service,
    WeatherRuleEngineService $ruleEngine
) {

    $cities = TrackedCity::all();

    foreach ($cities as $trackedCity) {
        $city = $trackedCity->city;

        $data = $service->current($city);
//     ApiLog::create([
//     'city' => $city,
//     'endpoint' => 'OpenWeather Current API',
//     'status_code' => isset($data['cod']) ? (int) $data['cod'] : 200,
//     'status' => isset($data['main']) ? 'success' : 'failed',
//     'response' => json_encode($data),
// ]);

        // VALIDASI API RESPONSE
        if (! isset($data['main'])) {

            $this->error("Failed fetch weather API for {$city}");

            continue;
        }

        // SIMPAN DATA WEATHER
        $weather = WeatherHistory::create([
            'tracked_city_id' => $trackedCity->id,
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

        // ANALISIS RULE ENGINE
        $analysis = $ruleEngine->analyze($weather);

        // UPDATE HASIL ANALISIS
        $weather->update([
            'recommendation' => $analysis['recommendation'],
            'insight' => $analysis['insight'],
            'risk_level' => $analysis['risk'],
        ]);

        $this->info("Weather fetched for {$city}");
    }

    $this->info('All weather fetched successfully.');
}
}