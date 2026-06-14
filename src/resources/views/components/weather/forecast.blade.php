@props(['forecast'])

@php
    $forecastByDay = [];
    foreach ($forecast['list'] ?? [] as $item) {
        $day = \Carbon\Carbon::parse($item['dt_txt'])->format('Y-m-d');
        if (! isset($forecastByDay[$day])) {
            $forecastByDay[$day] = $item;
        }
    }
    $dailyForecasts = array_values(array_slice($forecastByDay, 0, 5));
@endphp

<section aria-labelledby="forecast-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Forward Outlook</p>
        <h2 id="forecast-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">5 Day Forecast</h2>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @forelse($dailyForecasts as $item)
            <article class="rounded-2xl border border-white/10 bg-gradient-to-b from-sky-400/10 to-slate-950/20 p-5 text-center transition hover:-translate-y-1 hover:border-cyan-300/30">
                <p class="font-black text-white">{{ \Carbon\Carbon::parse($item['dt_txt'])->format('D') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ \Carbon\Carbon::parse($item['dt_txt'])->format('d M, H:i') }}</p>
                <img class="mx-auto h-20 w-20" src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}@2x.png" alt="{{ $item['weather'][0]['description'] }}">
                <p class="text-3xl font-black text-white">{{ number_format($item['main']['temp'], 0) }}°</p>
                <p class="mt-1 min-h-10 text-xs capitalize leading-5 text-slate-400">{{ $item['weather'][0]['description'] }}</p>
                <div class="mt-4 space-y-2 border-t border-white/10 pt-4 text-xs">
                    <div class="flex justify-between text-slate-400"><span>Humidity</span><strong class="text-white">{{ $item['main']['humidity'] }}%</strong></div>
                    <div class="flex justify-between text-slate-400"><span>Wind</span><strong class="text-white">{{ number_format($item['wind']['speed'], 1) }} m/s</strong></div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-white/10 py-12 text-center text-sm text-slate-500">
                Forecast data is currently unavailable.
            </div>
        @endforelse
    </div>
</section>
