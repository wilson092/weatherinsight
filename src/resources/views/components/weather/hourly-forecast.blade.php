@props(['forecast'])

@php
    $hourlyData = $forecast['hourly'] ?? [];
    $temperatures = array_map(fn ($item) => (float) data_get($item, 'main.temp', 0), $hourlyData);
    $minTemp = ! empty($temperatures) ? min($temperatures) : 0;
    $maxTemp = ! empty($temperatures) ? max($temperatures) : 0;
    $tempRange = max($maxTemp - $minTemp, 1);
    $pointCount = count($temperatures);
    $points = [];

    foreach ($temperatures as $index => $temperature) {
        $x = $pointCount > 1 ? ($index / ($pointCount - 1)) * 100 : 50;
        $y = 78 - ((($temperature - $minTemp) / $tempRange) * 48);
        $points[] = round($x, 2).','.round($y, 2);
    }

    $linePoints = implode(' ', $points);
    $areaPoints = $linePoints ? $linePoints.' 100,100 0,100' : '';
@endphp

<section
    x-data="{
        unit: 'C',
        hourlyData: {{ json_encode($hourlyData) }},
        convertTemp(temp) {
            if (this.unit === 'F') {
                return Math.round((temp * 9/5) + 32);
            }
            return Math.round(temp);
        }
    }"
    @temperature-unit-changed.window="unit = $event.detail"
    aria-labelledby="hourly-forecast-title"
    class="glass-panel min-h-[290px] overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6"
>
    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
                <x-heroicon-o-clock class="h-4 w-4" />
            </span>
            <h2 id="hourly-forecast-title" class="text-base font-black text-white">Hourly Forecast</h2>
        </div>
        <span class="text-xs font-semibold text-cyan-300">Next 24 Hours</span>
    </div>

    @if(! empty($hourlyData))
        <div class="rounded-3xl border border-white/10 bg-slate-950/28 px-4 py-4">
            <div class="relative h-32">
                <svg class="absolute inset-0 h-full w-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <defs>
                        <linearGradient id="hourlyTempFill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#22d3ee" stop-opacity=".24" />
                            <stop offset="100%" stop-color="#22d3ee" stop-opacity=".02" />
                        </linearGradient>
                    </defs>
                    <polygon points="{{ $areaPoints }}" fill="url(#hourlyTempFill)" />
                    <polyline points="{{ $linePoints }}" fill="none" stroke="#38bdf8" stroke-width="1.4" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

<div class="absolute inset-x-0 top-1 flex justify-between px-1">
    <template x-for="item in hourlyData" :key="item.dt">
        <span class="text-xs font-black text-white" x-text="convertTemp(item.main.temp) + '°'"></span>
    </template>
</div>

<div class="absolute inset-x-0 bottom-0 flex justify-between px-1">
    @foreach($hourlyData as $item)
        <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($item['dt_txt'])->format('H:i') }}</span>
    @endforeach
</div>
            </div>
        </div>

        <div class="-mx-5 mt-4 overflow-x-auto px-5 pb-1 sm:-mx-6 sm:px-6">
<div class="flex min-w-max gap-3">
    <template x-for="item in hourlyData" :key="item.dt">
        <article class="w-[92px] flex-none rounded-2xl border border-white/10 bg-slate-900/62 p-3 text-center shadow-xl shadow-slate-950/10 transition duration-300 hover:border-cyan-400/50 hover:bg-slate-800/70">
            <p class="text-xs font-black text-white" x-text="new Date(item.dt_txt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: false })"></p>
            <img
                class="mx-auto my-2 h-11 w-11 object-contain"
                :src="'https://openweathermap.org/img/wn/' + item.weather[0].icon + '@2x.png'"
                :alt="item.weather[0].description"
            >
            <p class="text-2xl font-black text-white" x-text="convertTemp(item.main.temp) + '°'"></p>
            <div class="mt-1 flex items-center justify-center gap-1 text-[11px] text-slate-400">
                <x-heroicon-o-beaker class="h-3 w-3 text-cyan-300" />
                <span x-text="Math.round(item.pop * 100) + '%'"></span>
            </div>
        </article>
    </template>
</div>
        </div>
    @else
        <div class="flex min-h-52 items-center justify-center rounded-3xl border border-dashed border-white/10 text-sm text-slate-500">
            Hourly forecast data is currently unavailable.
        </div>
    @endif
</section>