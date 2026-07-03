@props(['forecast'])

@php
    $forecastByDay = [];

    foreach ($forecast['list'] ?? [] as $item) {
        $day = \Carbon\Carbon::parse($item['dt_txt'])->format('Y-m-d');
        $forecastByDay[$day][] = $item;
    }

    $dailyForecasts = [];

    foreach ($forecastByDay as $forecasts) {
        $temps = array_map(fn ($item) => (float) data_get($item, 'main.temp'), $forecasts);
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
            $closestForecast['max_temp'] = max($temps);
            $closestForecast['min_temp'] = min($temps);
            $dailyForecasts[] = $closestForecast;
        }
    }

    $dailyForecasts = array_slice($dailyForecasts, 0, 5);
@endphp

<section aria-labelledby="forecast-title" class="glass-panel overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6">
    <div class="mb-4 flex items-center gap-2">
        <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
            <x-heroicon-o-calendar-days class="h-4 w-4" />
        </span>
        <h2 id="forecast-title" class="text-base font-black text-white">5 Day Forecast</h2>
    </div>

    @if(! empty($dailyForecasts))
        <div class="-mx-5 overflow-x-auto px-5 pb-1 sm:-mx-6 sm:px-6">
            <div class="grid min-w-[760px] grid-cols-5 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/28">
                @foreach($dailyForecasts as $item)
                    @php
                        $date = \Carbon\Carbon::parse($item['dt_txt']);
                        $pop = round((float) ($item['pop'] ?? 0) * 100);
                    @endphp

                    <article class="min-h-[166px] border-r border-white/10 bg-slate-900/36 p-4 text-center transition duration-300 last:border-r-0 hover:bg-slate-800/56">
                        <p class="text-xs font-black text-white">{{ $date->format('D, d M') }}</p>
                        <img
                            class="mx-auto mt-3 h-14 w-14 object-contain drop-shadow-lg"
                            src="https://openweathermap.org/img/wn/{{ data_get($item, 'weather.0.icon') }}@2x.png"
                            alt="{{ data_get($item, 'weather.0.description', 'Forecast weather') }}"
                        >
                        <div class="mt-2 flex items-baseline justify-center gap-1">
                            <span class="text-2xl font-black text-white">{{ number_format($item['max_temp'], 0) }}°</span>
                            <span class="text-base font-semibold text-slate-400">/ {{ number_format($item['min_temp'], 0) }}°</span>
                        </div>
                        <p class="mt-1 h-9 overflow-hidden text-xs capitalize leading-4 text-slate-300">{{ data_get($item, 'weather.0.description', 'Unavailable') }}</p>
                        <div class="mt-3 flex items-center justify-center gap-4 text-[11px] text-slate-300">
                            <span class="inline-flex items-center gap-1">
                                <x-heroicon-o-beaker class="h-3.5 w-3.5 text-cyan-300" />
                                {{ data_get($item, 'main.humidity', '--') }}%
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <x-heroicon-o-bars-3-bottom-left class="h-3.5 w-3.5 text-sky-300" />
                                {{ number_format((float) data_get($item, 'wind.speed', 0) * 3.6, 0) }} km/h
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @else
        <div class="flex min-h-40 items-center justify-center rounded-2xl border border-dashed border-white/10 text-sm text-slate-500">
            Forecast data is currently unavailable.
        </div>
    @endif
</section>
