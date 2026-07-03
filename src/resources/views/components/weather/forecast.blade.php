@props(['forecast'])

@php
    $forecastByDay = [];
    // Group forecasts by day
    foreach ($forecast['list'] ?? [] as $item) {
        $day = \Carbon\Carbon::parse($item['dt_txt'])->format('Y-m-d');
        $forecastByDay[$day][] = $item;
    }

    $dailyForecasts = [];
    foreach ($forecastByDay as $day => $forecasts) {
        // Calculate min/max temp for the day
        $temps = array_column(array_column($forecasts, 'main'), 'temp');
        $maxTemp = !empty($temps) ? max($temps) : null;
        $minTemp = !empty($temps) ? min($temps) : null;
        
        // Find the forecast closest to noon (12:00) for the icon and description
        $closestForecast = null;
        $smallestDiff = PHP_INT_MAX;

        foreach ($forecasts as $item) {
            $time = \Carbon\Carbon::parse($item['dt_txt']);
            $diff = abs($time->hour - 12);

            if ($diff < $smallestDiff) {
                $smallestDiff = $diff;
                $closestForecast = $item;
            }
        }

        if ($closestForecast) {
            $closestForecast['max_temp'] = $maxTemp;
            $closestForecast['min_temp'] = $minTemp;
            $dailyForecasts[] = $closestForecast;
        }
    }

    // Ensure we only take up to 5 days
    $dailyForecasts = array_slice($dailyForecasts, 0, 5);
    
    // Weather condition gradient backgrounds
    $weatherGradients = [
        'Clear' => 'from-sky-400/20 via-blue-300/15 to-cyan-200/10',
        'Clouds' => 'from-slate-500/20 via-slate-400/15 to-slate-300/10',
        'Rain' => 'from-blue-600/20 via-slate-500/15 to-blue-900/10',
        'Drizzle' => 'from-slate-500/20 via-blue-600/15 to-slate-400/10',
        'Thunderstorm' => 'from-purple-800/20 via-slate-700/15 to-slate-900/10',
        'Snow' => 'from-blue-100/20 via-slate-50/15 to-cyan-100/10',
        'Mist' => 'from-slate-400/20 via-slate-300/15 to-slate-200/10',
        'Fog' => 'from-slate-400/20 via-slate-300/15 to-slate-200/10',
    ];
@endphp

<section aria-labelledby="forecast-title" class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8">
    <!-- Background decorations -->
    <div class="absolute -left-20 top-0 h-64 w-64 rounded-full bg-sky-400/10 blur-3xl"></div>
    <div class="absolute -right-20 bottom-0 h-56 w-56 rounded-full bg-cyan-400/10 blur-3xl"></div>

    <div class="relative">
        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Forward Outlook</p>
            <h2 id="forecast-title" class="mt-2 text-3xl font-black text-white sm:text-4xl">5 Day Forecast</h2>
        </div>

        @if(!empty($dailyForecasts))
            <!-- Desktop & Tablet: Grid Layout -->
            <div class="hidden gap-4 sm:grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach($dailyForecasts as $item)
                    @php
                        $date = \Carbon\Carbon::parse($item['dt_txt']);
                        $weatherMain = $item['weather'][0]['main'] ?? 'Clouds';
                        $gradient = $weatherGradients[$weatherMain] ?? $weatherGradients['Clouds'];
                    @endphp
                    
                    <article class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b {{ $gradient }} p-6 text-center backdrop-blur-sm transition-all duration-300 hover:-translate-y-2 hover:border-cyan-300/40 hover:shadow-2xl hover:shadow-cyan-500/20">
                        <!-- Day Name -->
                        <div class="mb-4">
                            <p class="text-xl font-black text-white">{{ $date->format('l') }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ $date->format('d F') }}</p>
                        </div>

                        <!-- Weather Icon -->
                        <div class="relative mx-auto mb-4 flex h-28 w-28 items-center justify-center">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-br from-cyan-300/20 to-teal-300/10 blur-xl transition-all group-hover:scale-110"></div>
                            <img 
                                class="relative h-full w-full object-contain drop-shadow-[0_10px_20px_rgba(8,145,178,.3)] transition-transform group-hover:scale-110" 
                                src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}@4x.png" 
                                alt="{{ $item['weather'][0]['description'] }}"
                            >
                        </div>

                        <!-- Temperature Range -->
                        <div class="mb-3">
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-4xl font-black text-white">{{ number_format($item['max_temp'], 0) }}°</span>
                                <span class="text-2xl font-bold text-slate-400">/</span>
                                <span class="text-2xl font-bold text-slate-300">{{ number_format($item['min_temp'], 0) }}°</span>
                            </div>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Max / Min</p>
                        </div>

                        <!-- Weather Description -->
                        <p class="mb-4 min-h-10 text-sm font-medium capitalize leading-5 text-slate-300">
                            {{ $item['weather'][0]['description'] }}
                        </p>

                        <!-- Metrics -->
                        <div class="space-y-2 border-t border-white/10 pt-4">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5 text-slate-400">
                                    <x-heroicon-o-beaker class="h-3.5 w-3.5 text-sky-300" />
                                    <span>Humidity</span>
                                </div>
                                <strong class="font-bold text-white">{{ $item['main']['humidity'] }}%</strong>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5 text-slate-400">
                                    <x-heroicon-o-arrow-trending-up class="h-3.5 w-3.5 text-teal-300" />
                                    <span>Wind</span>
                                </div>
                                <strong class="font-bold text-white">{{ number_format($item['wind']['speed'], 1) }} m/s</strong>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Mobile: Horizontal Scroll -->
            <div class="relative -mx-6 px-6 sm:hidden">
                <div class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-white/10">
                    @foreach($dailyForecasts as $item)
                        @php
                            $date = \Carbon\Carbon::parse($item['dt_txt']);
                            $weatherMain = $item['weather'][0]['main'] ?? 'Clouds';
                            $gradient = $weatherGradients[$weatherMain] ?? $weatherGradients['Clouds'];
                        @endphp
                        
                        <article class="w-72 flex-none snap-center overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b {{ $gradient }} p-6 text-center backdrop-blur-sm">
                            <!-- Day Name -->
                            <div class="mb-4">
                                <p class="text-xl font-black text-white">{{ $date->format('l') }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ $date->format('d F') }}</p>
                            </div>

                            <!-- Weather Icon -->
                            <div class="relative mx-auto mb-4 flex h-28 w-28 items-center justify-center">
                                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-cyan-300/20 to-teal-300/10 blur-xl"></div>
                                <img 
                                    class="relative h-full w-full object-contain drop-shadow-[0_10px_20px_rgba(8,145,178,.3)]" 
                                    src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}@4x.png" 
                                    alt="{{ $item['weather'][0]['description'] }}"
                                >
                            </div>

                            <!-- Temperature Range -->
                            <div class="mb-3">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-4xl font-black text-white">{{ number_format($item['max_temp'], 0) }}°</span>
                                    <span class="text-2xl font-bold text-slate-400">/</span>
                                    <span class="text-2xl font-bold text-slate-300">{{ number_format($item['min_temp'], 0) }}°</span>
                                </div>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Max / Min</p>
                            </div>

                            <!-- Weather Description -->
                            <p class="mb-4 min-h-10 text-sm font-medium capitalize leading-5 text-slate-300">
                                {{ $item['weather'][0]['description'] }}
                            </p>

                            <!-- Metrics -->
                            <div class="space-y-2 border-t border-white/10 pt-4">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-1.5 text-slate-400">
                                        <x-heroicon-o-beaker class="h-3.5 w-3.5 text-sky-300" />
                                        <span>Humidity</span>
                                    </div>
                                    <strong class="font-bold text-white">{{ $item['main']['humidity'] }}%</strong>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-1.5 text-slate-400">
                                        <x-heroicon-o-arrow-trending-up class="h-3.5 w-3.5 text-teal-300" />
                                        <span>Wind</span>
                                    </div>
                                    <strong class="font-bold text-white">{{ number_format($item['wind']['speed'], 1) }} m/s</strong>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-white/10 py-16 text-center">
                <x-heroicon-o-cloud class="mx-auto h-16 w-16 text-slate-600" />
                <p class="mt-4 text-lg font-black text-white">Forecast Unavailable</p>
                <p class="mt-2 text-sm text-slate-500">Weather forecast data is currently unavailable.</p>
            </div>
        @endif
    </div>
</section>