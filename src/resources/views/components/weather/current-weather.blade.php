@props(['latest'])

@php
    // Determine background based on weather condition
    $weatherCondition = strtolower($latest?->weather_main ?? 'clouds');
    $timeOfDay = now()->hour >= 18 || now()->hour < 6 ? 'night' : 'day';
    
    // Background gradients based on weather
    $backgrounds = [
        'clear' => 'from-sky-400 via-blue-300 to-cyan-200',
        'clouds' => 'from-slate-600 via-slate-500 to-slate-400',
        'rain' => 'from-slate-700 via-slate-600 to-blue-900',
        'drizzle' => 'from-slate-600 via-blue-700 to-slate-500',
        'thunderstorm' => 'from-purple-900 via-slate-800 to-slate-900',
        'snow' => 'from-blue-200 via-slate-100 to-cyan-100',
        'mist' => 'from-slate-500 via-slate-400 to-slate-300',
        'fog' => 'from-slate-500 via-slate-400 to-slate-300',
    ];
    
    $bgGradient = $backgrounds[$weatherCondition] ?? $backgrounds['clouds'];
    
    if ($timeOfDay === 'night' && $weatherCondition === 'clear') {
        $bgGradient = 'from-indigo-900 via-blue-900 to-slate-900';
    }
    
    // 6 Weather metrics
    $metrics = [
        [
            'label' => 'Humidity',
            'value' => $latest ? number_format($latest->humidity, 0).'%' : '--',
            'icon' => 'heroicon-o-beaker',
            'iconColor' => 'text-sky-300'
        ],
        [
            'label' => 'Wind Speed', 
            'value' => $latest ? number_format($latest->wind_speed, 1).' m/s' : '--',
            'icon' => 'heroicon-o-bars-3-bottom-left',
            'iconColor' => 'text-teal-300'
        ],
        [
            'label' => 'Pressure',
            'value' => $latest ? number_format($latest->pressure, 0).' hPa' : '--',
            'icon' => 'heroicon-o-chart-bar',
            'iconColor' => 'text-cyan-300'
        ],
        [
            'label' => 'Visibility',
            'value' => $latest && isset($latest->visibility) ? number_format($latest->visibility / 1000, 1).' km' : 'N/A',
            'icon' => 'heroicon-o-eye',
            'iconColor' => 'text-blue-300'
        ],
        [
            'label' => 'Feels Like',
            'value' => $latest && isset($latest->feels_like) ? number_format($latest->feels_like, 0).'°C' : '--',
            'icon' => 'heroicon-o-hand-raised',
            'iconColor' => 'text-orange-300'
        ],
        [
            'label' => 'Risk Level',
            'value' => $latest?->risk_level ?? '--',
            'icon' => 'heroicon-o-shield-check',
            'iconColor' => 'text-rose-300'
        ],
    ];
@endphp

<section aria-labelledby="current-weather-title" class="glass-panel relative overflow-hidden rounded-3xl">
    <!-- Dynamic Background with Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $bgGradient }} opacity-20"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-950/60 to-slate-950/90"></div>
    
    <!-- Decorative Blurs -->
    <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-cyan-400/15 blur-3xl"></div>
    <div class="absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-teal-400/15 blur-3xl"></div>

    <div class="relative p-6 sm:p-8">
        <!-- Location & Time -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-slate-300">
                <x-heroicon-o-map-pin class="h-5 w-5 text-cyan-300" />
                <span class="text-lg font-bold text-white">{{ $latest?->city ?? 'No city data' }}</span>
                @if($latest?->country)
                    <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs font-semibold uppercase">{{ $latest->country }}</span>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-400">{{ now()->format('l, d F Y • H:i') }}</p>
        </div>

        <!-- Main Weather Display -->
        <div class="mb-8 flex items-center justify-between">
            <div class="flex-1">
                <!-- Temperature - The Star of the Show -->
                <div class="flex items-start">
                    <h2 id="current-weather-title" class="text-8xl font-black tracking-tighter text-white sm:text-9xl lg:text-[10rem]">
                        {{ $latest ? number_format($latest->temperature, 0) : '--' }}
                    </h2>
                    <span class="mt-4 text-5xl font-black text-cyan-300 sm:mt-6 sm:text-6xl lg:mt-8 lg:text-7xl">°</span>
                </div>
                
                <!-- Weather Description -->
                <div class="mt-4">
                    <p class="text-2xl font-bold capitalize text-white sm:text-3xl">{{ $latest?->weather_main ?? 'Unavailable' }}</p>
                    <p class="mt-1 text-lg capitalize text-slate-300">{{ $latest?->weather_description ?? 'Waiting for weather data' }}</p>
                </div>
            </div>

            <!-- Weather Icon -->
            <div class="relative ml-4 hidden sm:block">
                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-cyan-300/20 to-teal-300/10 blur-2xl"></div>
                @if($latest?->weather_icon)
                    <img
                        class="relative h-32 w-32 object-contain drop-shadow-[0_20px_25px_rgba(8,145,178,.3)] lg:h-40 lg:w-40"
                        src="https://openweathermap.org/img/wn/{{ $latest->weather_icon }}@4x.png"
                        alt="{{ $latest->weather_description ?? 'Current weather' }}"
                    >
                @else
                    <x-heroicon-o-cloud class="relative h-24 w-24 text-cyan-200/60 lg:h-32 lg:w-32" />
                @endif
            </div>
        </div>

        <!-- 6 Metrics Grid (2x3) -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach($metrics as $metric)
                <div class="group rounded-2xl border border-white/10 bg-gradient-to-br from-white/[.07] to-white/[.02] p-4 backdrop-blur-sm transition-all hover:border-cyan-300/30 hover:bg-white/[.12]">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 flex-none items-center justify-center rounded-xl bg-white/10 transition-colors group-hover:bg-cyan-400/20">
                            <x-dynamic-component :component="$metric['icon']" class="h-5 w-5 {{ $metric['iconColor'] }}" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $metric['label'] }}</p>
                            <p class="truncate text-base font-black text-white sm:text-lg">{{ $metric['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>