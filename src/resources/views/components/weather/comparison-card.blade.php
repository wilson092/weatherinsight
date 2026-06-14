@props(['weather', 'label', 'source' => null])

<article class="rounded-3xl border border-white/10 bg-slate-950/25 p-6">
    <div class="mb-6 flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">{{ $label }}</p>
            <h3 class="mt-2 text-2xl font-black text-white">{{ $weather?->city ?? 'Select a city' }}</h3>
        </div>
        @if($source)
            <span class="rounded-full border border-cyan-300/15 bg-cyan-300/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-cyan-200">
                {{ $source }}
            </span>
        @endif
    </div>

    @if($weather)
        @php
            $rows = [
                ['label' => 'Temperature', 'value' => number_format($weather->temperature, 1).' °C'],
                ['label' => 'Humidity', 'value' => number_format($weather->humidity, 0).' %'],
                ['label' => 'Pressure', 'value' => number_format($weather->pressure, 0).' hPa'],
                ['label' => 'Wind Speed', 'value' => number_format($weather->wind_speed, 1).' m/s'],
                ['label' => 'Risk Score', 'value' => $weather->risk_score.'/100'],
            ];
        @endphp
        <div class="space-y-2">
            @foreach($rows as $row)
                <div class="flex items-center justify-between rounded-xl bg-white/[.04] px-4 py-3">
                    <span class="text-sm text-slate-400">{{ $row['label'] }}</span>
                    <span class="text-sm font-black text-white">{{ $row['value'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex items-center justify-between border-t border-white/10 pt-5">
            <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Risk Category</span>
            <span class="text-sm font-black {{ $weather->risk_level === 'HIGH' ? 'text-rose-300' : ($weather->risk_level === 'MEDIUM' ? 'text-amber-300' : 'text-emerald-300') }}">
                {{ $weather->risk_level }}
            </span>
        </div>
    @else
        <div class="flex min-h-72 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/[.02] px-6 text-center">
            <x-heroicon-o-arrows-right-left class="h-12 w-12 text-slate-700" />
            <p class="mt-4 font-bold text-slate-300">Ready for comparison</p>
            <p class="mt-2 max-w-xs text-sm leading-6 text-slate-500">Enter another city to use its latest history or retrieve current OpenWeather data.</p>
        </div>
    @endif
</article>
