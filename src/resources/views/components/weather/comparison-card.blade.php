@props(['weather', 'analysis' => null, 'label', 'source' => null, 'accentColor' => 'cyan'])

@php
    $accentStyles = [
        'cyan' => [
            'border' => 'border-cyan-400/30',
            'bg' => 'bg-gradient-to-br from-cyan-400/10 to-cyan-500/5',
            'badge' => 'border-cyan-300/15 bg-cyan-300/10 text-cyan-200',
            'icon' => 'bg-cyan-400/20 text-cyan-300',
        ],
        'teal' => [
            'border' => 'border-teal-400/30',
            'bg' => 'bg-gradient-to-br from-teal-400/10 to-teal-500/5',
            'badge' => 'border-teal-300/15 bg-teal-300/10 text-teal-200',
            'icon' => 'bg-teal-400/20 text-teal-300',
        ],
    ];

    $style = $accentStyles[$accentColor] ?? $accentStyles['cyan'];

    $riskColors = [
        'Low' => 'text-emerald-300',
        'Medium' => 'text-amber-300',
        'High' => 'text-rose-300',
        'Extreme' => 'text-purple-300',
    ];
@endphp

<article class="glass-panel relative overflow-hidden rounded-3xl border p-6 sm:p-8 {{ $style['border'] }} {{ $style['bg'] }}">
    <!-- Background decoration -->
    <div class="absolute -right-12 top-0 h-40 w-40 rounded-full blur-3xl {{ $style['icon'] }} opacity-20"></div>

    <div class="relative">
        <!-- Header -->
        <div class="mb-6 flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl {{ $style['icon'] }}">
                    <x-heroicon-o-map-pin class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">{{ $label }}</p>
                    <h3 class="mt-1 text-2xl font-black text-white">{{ $weather?->city ?? 'Select a city' }}</h3>
                </div>
            </div>
            @if($source)
                <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $style['badge'] }}">
                    {{ $source }}
                </span>
            @endif
        </div>

        @if($weather)
            <!-- Weather Icon & Description -->
            @if($weather->weather_icon && $weather->weather_description)
                <div class="mb-6 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <img 
                        src="https://openweathermap.org/img/wn/{{ $weather->weather_icon }}@2x.png" 
                        alt="{{ $weather->weather_description }}"
                        class="h-16 w-16"
                    >
                    <div>
                        <p class="text-lg font-black text-white capitalize">{{ $weather->weather_description }}</p>
                        <p class="text-sm text-slate-400">{{ $weather->weather_main }}</p>
                    </div>
                </div>
            @endif

            <!-- Main Metrics Grid -->
            <div class="mb-6 grid gap-3 sm:grid-cols-2">
                <!-- Temperature -->
                <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-fire class="h-5 w-5 text-orange-300" />
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Temperature</span>
                    </div>
                    <p class="mt-2 text-2xl font-black text-white">{{ number_format($weather->temperature, 1) }}°C</p>
                </div>

                <!-- Humidity -->
                <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-beaker class="h-5 w-5 text-blue-300" />
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Humidity</span>
                    </div>
                    <p class="mt-2 text-2xl font-black text-white">{{ number_format($weather->humidity, 0) }}%</p>
                </div>

                <!-- Pressure -->
                <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-arrow-down-circle class="h-5 w-5 text-indigo-300" />
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pressure</span>
                    </div>
                    <p class="mt-2 text-2xl font-black text-white">{{ number_format($weather->pressure, 0) }} hPa</p>
                </div>

                <!-- Wind Speed -->
                <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-arrow-trending-up class="h-5 w-5 text-teal-300" />
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Wind Speed</span>
                    </div>
                    <p class="mt-2 text-2xl font-black text-white">{{ number_format($weather->wind_speed, 1) }} m/s</p>
                </div>
            </div>

            <!-- Risk Analysis Section -->
            @if($analysis)
                <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-shield-exclamation class="h-5 w-5 text-slate-400" />
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Risk Analysis</span>
                        </div>
                        <span class="text-sm font-black {{ $riskColors[$analysis['risk']] ?? 'text-slate-300' }}">
                            {{ $analysis['risk'] ?? 'Unknown' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="h-2 overflow-hidden rounded-full bg-slate-800">
                                <div 
                                    class="h-full rounded-full transition-all {{ $analysis['risk'] === 'Extreme' ? 'bg-purple-400' : ($analysis['risk'] === 'High' ? 'bg-rose-400' : ($analysis['risk'] === 'Medium' ? 'bg-amber-400' : 'bg-emerald-400')) }}"
                                    style="width: {{ min(($analysis['score'] ?? 0), 100) }}%"
                                ></div>
                            </div>
                        </div>
                        <span class="text-sm font-black text-white">{{ $analysis['score'] ?? 0 }}/100</span>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="flex min-h-72 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/[.02] px-6 text-center">
                <x-heroicon-o-arrows-right-left class="h-12 w-12 text-slate-700" />
                <p class="mt-4 font-bold text-slate-300">Ready for comparison</p>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-500">Enter a city to see its weather data and risk analysis</p>
            </div>
        @endif
    </div>
</article>