<?php

use App\Models\TrackedCity;
use App\Models\User;
use App\Models\WeatherHistory;
use App\Services\Weather\LatestWeatherSnapshotService;
use App\Services\Weather\WeatherAlertService;
use App\Services\Weather\WeatherComparisonService;
use App\Services\Weather\WeatherLeaderboardService;
use App\Services\Weather\WeatherRiskAnalysisService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function weatherAttributes(array $overrides = []): array
{
    return array_merge([
        'temperature' => 30,
        'humidity' => 70,
        'pressure' => 1010,
        'wind_speed' => 5,
        'weather_main' => 'Clouds',
        'weather_description' => 'scattered clouds',
        'weather_icon' => '03d',
    ], $overrides);
}

function createWeather(string $city, array $overrides = []): WeatherHistory
{
    $trackedCity = TrackedCity::firstOrCreate(['city' => $city]);

    return WeatherHistory::create(array_merge(
        weatherAttributes(),
        [
            'tracked_city_id' => $trackedCity->id,
            'city' => $city,
            'recorded_at' => now(),
        ],
        $overrides,
    ));
}

it('calculates binary weather risk scores at the configured strict thresholds', function () {
    $service = app(WeatherRiskAnalysisService::class);

    $safe = $service->analyze(weatherAttributes([
        'temperature' => 35,
        'humidity' => 85,
        'pressure' => 1000,
        'wind_speed' => 20,
    ]));

    $medium = $service->analyze(weatherAttributes([
        'temperature' => 36,
        'humidity' => 86,
    ]));

    $high = $service->analyze(weatherAttributes([
        'temperature' => 36,
        'humidity' => 86,
        'pressure' => 999,
        'wind_speed' => 21,
    ]));

    expect($safe)->toMatchArray(['score' => 0, 'level' => 'LOW'])
        ->and($medium)->toMatchArray(['score' => 50, 'level' => 'MEDIUM'])
        ->and($high)->toMatchArray(['score' => 100, 'level' => 'HIGH']);
});

it('persists risk score and category through the weather history observer', function () {
    $weather = createWeather('Jakarta', [
        'temperature' => 36,
        'humidity' => 90,
        'pressure' => 990,
    ]);

    expect($weather->risk_score)->toBe(75)
        ->and($weather->risk_level)->toBe('HIGH');
});

it('creates automatic alerts from the latest weather readings', function () {
    $weather = createWeather('Surabaya', [
        'temperature' => 37,
        'humidity' => 94,
        'wind_speed' => 24,
        'weather_main' => 'Thunderstorm',
    ]);

    $alerts = collect(app(WeatherAlertService::class)->forWeather($weather));

    expect($alerts->pluck('key')->all())->toBe([
        'heavy-rain',
        'extreme-heat',
        'strong-wind',
        'high-humidity',
    ]);
});

it('uses only the latest snapshot of each city for leaderboards', function () {
    createWeather('Jakarta', [
        'temperature' => 40,
        'recorded_at' => now()->subHour(),
    ]);
    createWeather('Jakarta', [
        'temperature' => 31,
        'recorded_at' => now(),
    ]);
    createWeather('Bandung', [
        'temperature' => 33,
        'recorded_at' => now(),
    ]);

    $snapshots = app(LatestWeatherSnapshotService::class)->all();
    $leaderboard = app(WeatherLeaderboardService::class)->rankings();

    expect($snapshots)->toHaveCount(2)
        ->and($snapshots->firstWhere('city', 'Jakarta')->temperature)->toBe(31.0)
        ->and($leaderboard['hottest']->first()->city)->toBe('Bandung');
});

it('fetches and stores a comparison city when no history exists', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'main' => ['temp' => 36, 'humidity' => 88, 'pressure' => 1012],
            'wind' => ['speed' => 8],
            'weather' => [[
                'main' => 'Rain',
                'description' => 'moderate rain',
                'icon' => '10d',
            ]],
        ]),
    ]);

    $result = app(WeatherComparisonService::class)->findOrFetch('Medan');

    expect($result['source'])->toBe('realtime')
        ->and($result['weather']->risk_score)->toBe(50)
        ->and(TrackedCity::where('city', 'Medan')->exists())->toBeTrue()
        ->and(WeatherHistory::where('city', 'Medan')->exists())->toBeTrue();
});

it('renders the weather intelligence dashboard sections', function () {
    $this->seed(RoleSeeder::class);

    Http::fake(function ($request) {
        if (str_contains($request->url(), '/forecast')) {
            return Http::response(['list' => []]);
        }

        return Http::response([
            'main' => ['temp' => 32, 'humidity' => 75, 'pressure' => 1010],
            'wind' => ['speed' => 6],
            'weather' => [[
                'main' => 'Clouds',
                'description' => 'few clouds',
                'icon' => '02d',
            ]],
        ]);
    });

    $response = $this->actingAs(User::factory()->create())->get('/?city=Jakarta');

    $response->assertOk()
        ->assertSee('Weather Intelligence')
        ->assertSee('Weather Risk Score')
        ->assertSee('Weather Alert Center')
        ->assertSee('Weather Comparison')
        ->assertSee('Extreme Weather Leaderboard')
        ->assertSee('Weather History');
});
