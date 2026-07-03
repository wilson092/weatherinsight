@props(['latest'])

@php
    $condition = strtolower($latest?->weather_main ?? 'clouds');
    $weatherGradient = match (true) {
        str_contains($condition, 'clear') => 'from-sky-500/30 via-blue-900/40 to-slate-950/80',
        str_contains($condition, 'rain'), str_contains($condition, 'drizzle') => 'from-cyan-700/25 via-slate-800/55 to-slate-950/90',
        str_contains($condition, 'thunder') => 'from-violet-700/25 via-slate-800/60 to-slate-950/95',
        default => 'from-blue-500/24 via-slate-800/54 to-slate-950/90',
    };

    $recordedAt = $latest?->recorded_at ?? now();
    $metrics = [
        [
            'label' => 'Humidity',
            'value' => $latest ? number_format($latest->humidity, 0).'%' : '--',
            'icon' => 'heroicon-o-beaker',
            'hint' => null,
            'tone' => 'text-cyan-300',
        ],
        [
            'label' => 'Pressure',
            'value' => $latest ? number_format($latest->pressure, 0).' hPa' : '--',
            'icon' => 'heroicon-o-chart-bar',
            'hint' => null,
            'tone' => 'text-amber-300',
        ],
        [
            'label' => 'Wind',
            'value' => $latest ? number_format($latest->wind_speed * 3.6, 0).' km/h' : '--',
            'icon' => 'heroicon-o-bars-3-bottom-left',
            'hint' => $latest ? number_format($latest->wind_speed, 1).' m/s' : null,
            'tone' => 'text-sky-300',
        ],
    ];
@endphp

<section aria-labelledby="current-weather-title" class="glass-panel group relative min-h-[290px] overflow-hidden rounded-3xl transition duration-300 hover:border-cyan-400/50">
    <div class="absolute inset-0 bg-gradient-to-br {{ $weatherGradient }}"></div>
    <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(2,8,23,.12),rgba(14,116,144,.18),rgba(2,8,23,.72))]"></div>
    <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-slate-950/80 to-transparent"></div>

    <div class="relative flex h-full flex-col p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 text-sm font-bold text-cyan-100">
                    <x-heroicon-o-map-pin class="h-4 w-4 flex-none text-cyan-300" />
                    <span class="truncate">{{ $latest?->city ?? 'No city data' }}</span>
                    @if($latest?->country)
                        <span class="text-cyan-200/60">{{ $latest->country }}</span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-slate-300">{{ $recordedAt->format('l, d F Y') }} &bull; {{ $recordedAt->format('H:i') }}</p>
            </div>

            @if($latest?->weather_icon)
                <img
                    class="h-24 w-24 flex-none object-contain drop-shadow-[0_18px_22px_rgba(2,8,23,.35)] sm:h-32 sm:w-32"
                    src="https://openweathermap.org/img/wn/{{ $latest->weather_icon }}@4x.png"
                    alt="{{ $latest->weather_description ?? 'Current weather' }}"
                >
            @else
                <x-heroicon-o-cloud class="h-20 w-20 flex-none text-cyan-100/80" />
            @endif
        </div>

        <div class="mt-2 flex flex-1 items-center justify-between gap-5">
            <div>
                <div class="flex items-start">
                    <span id="current-weather-title" class="text-7xl font-black leading-none tracking-normal text-white sm:text-8xl">
                        {{ $latest ? number_format($latest->temperature, 0) : '--' }}
                    </span>
                    <span class="mt-2 text-3xl font-black text-white sm:text-4xl">°C</span>
                </div>
                <p class="mt-2 text-xl font-black capitalize text-white">{{ $latest?->weather_main ?? 'Unavailable' }}</p>
                <p class="mt-1 max-w-xs text-sm capitalize text-slate-200">{{ $latest?->weather_description ?? 'Waiting for weather data' }}</p>
            </div>

            <div class="hidden rounded-full border border-white/10 bg-slate-950/35 px-4 py-2 text-xs font-bold text-slate-200 sm:block">
                {{ $latest?->risk_level ?? 'Monitoring' }}
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
            @foreach($metrics as $metric)
                <article class="rounded-2xl border border-white/10 bg-slate-950/38 p-3 shadow-xl shadow-slate-950/10 transition duration-300 hover:border-cyan-400/50 hover:bg-slate-900/55">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 flex-none items-center justify-center rounded-full bg-white/8">
                            <x-dynamic-component :component="$metric['icon']" class="h-4 w-4 {{ $metric['tone'] }}" />
                        </span>
                        <span class="truncate text-xs font-semibold text-slate-300">{{ $metric['label'] }}</span>
                    </div>
                    <p class="mt-2 truncate text-base font-black text-white">{{ $metric['value'] }}</p>
                    @if($metric['hint'])
                        <p class="mt-0.5 truncate text-[11px] text-slate-400">{{ $metric['hint'] }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
