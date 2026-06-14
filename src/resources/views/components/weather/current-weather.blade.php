@props(['latest'])

<section aria-labelledby="current-weather-title" class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8 lg:p-10">
    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>
    <div class="absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-teal-400/10 blur-3xl"></div>

    <div class="relative grid gap-8 lg:grid-cols-[1.15fr_.85fr] lg:items-center">
        <div>
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/10 px-3 py-1.5 text-xs font-bold uppercase tracking-widest text-cyan-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,.9)]"></span>
                    Live Conditions
                </span>
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-slate-300">
                    <x-heroicon-o-map-pin class="h-5 w-5 text-cyan-300" />
                    {{ $latest?->city ?? 'No city data' }}
                </span>
            </div>

            <div class="flex items-end gap-4">
                <h2 id="current-weather-title" class="text-7xl font-black tracking-tighter text-white sm:text-8xl lg:text-9xl">
                    {{ $latest ? number_format($latest->temperature, 0) : '--' }}<span class="align-top text-4xl text-cyan-300">°</span>
                </h2>
                <div class="pb-3">
                    <p class="text-xl font-bold text-white">{{ $latest?->weather_main ?? 'Unavailable' }}</p>
                    <p class="mt-1 capitalize text-slate-400">{{ $latest?->weather_description ?? 'Waiting for weather data' }}</p>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @php
                    $metrics = [
                        ['label' => 'Humidity', 'value' => $latest ? number_format($latest->humidity, 0).'%' : '--', 'icon' => 'heroicon-o-beaker'],
                        ['label' => 'Pressure', 'value' => $latest ? number_format($latest->pressure, 0).' hPa' : '--', 'icon' => 'heroicon-o-chart-bar'],
                        ['label' => 'Wind', 'value' => $latest ? number_format($latest->wind_speed, 1).' m/s' : '--', 'icon' => 'heroicon-o-bars-3-bottom-left'],
                        ['label' => 'Risk', 'value' => $latest?->risk_level ?? '--', 'icon' => 'heroicon-o-shield-check'],
                    ];
                @endphp

                @foreach($metrics as $metric)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/25 p-4">
                        <x-dynamic-component :component="$metric['icon']" class="mb-3 h-5 w-5 text-cyan-300" />
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $metric['label'] }}</p>
                        <p class="mt-1 font-bold text-white">{{ $metric['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative flex min-h-72 items-center justify-center">
            <div class="absolute h-64 w-64 rounded-full border border-cyan-300/15 bg-gradient-to-br from-cyan-300/15 to-teal-300/5 shadow-[0_0_80px_rgba(34,211,238,.13)]"></div>
            @if($latest?->weather_icon)
                <img
                    class="relative h-64 w-64 object-contain drop-shadow-[0_25px_30px_rgba(8,145,178,.25)]"
                    src="https://openweathermap.org/img/wn/{{ $latest->weather_icon }}@4x.png"
                    alt="{{ $latest->weather_description ?? 'Current weather' }}"
                >
            @else
                <x-heroicon-o-cloud class="relative h-32 w-32 text-cyan-200/60" />
            @endif
        </div>
    </div>

    <div class="relative mt-8 grid gap-4 border-t border-white/10 pt-6 md:grid-cols-2">
        <div class="rounded-2xl bg-white/[.04] p-5">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-300">Recommendation</p>
            <p class="mt-2 text-sm leading-6 text-slate-300">{{ $latest?->recommendation ?? 'No recommendation available.' }}</p>
        </div>
        <div class="rounded-2xl bg-white/[.04] p-5">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-300">Weather Insight</p>
            <p class="mt-2 text-sm leading-6 text-slate-300">{{ $latest?->insight ?? 'No insight available.' }}</p>
        </div>
    </div>
</section>
