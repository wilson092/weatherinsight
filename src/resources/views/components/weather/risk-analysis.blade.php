@props(['latest', 'analysis'])

@php
    $score = (int) ($analysis['score'] ?? $latest?->risk_score ?? 0);
    $level = $analysis['level'] ?? $latest?->risk_level ?? 'LOW';
    $conditions = $analysis['conditions'] ?? [];
    $tone = match ($level) {
        'HIGH' => ['text' => 'text-rose-300', 'bar' => 'from-rose-500 to-orange-400', 'ring' => 'border-rose-400/25 bg-rose-500/10'],
        'MEDIUM' => ['text' => 'text-amber-300', 'bar' => 'from-amber-400 to-yellow-300', 'ring' => 'border-amber-400/25 bg-amber-500/10'],
        default => ['text' => 'text-emerald-300', 'bar' => 'from-emerald-400 to-teal-300', 'ring' => 'border-emerald-400/25 bg-emerald-500/10'],
    };
    $factors = [
        ['key' => 'temperature', 'label' => 'Temperature above 35°C', 'value' => $latest ? number_format($latest->temperature, 1).'°C' : '--'],
        ['key' => 'humidity', 'label' => 'Humidity above 85%', 'value' => $latest ? number_format($latest->humidity, 0).'%' : '--'],
        ['key' => 'wind_speed', 'label' => 'Wind above 20 m/s', 'value' => $latest ? number_format($latest->wind_speed, 1).' m/s' : '--'],
        ['key' => 'pressure', 'label' => 'Pressure outside 1000–1020', 'value' => $latest ? number_format($latest->pressure, 0).' hPa' : '--'],
    ];
@endphp

<section aria-labelledby="risk-analysis-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Risk Analysis</p>
            <h2 id="risk-analysis-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Weather Risk Score</h2>
        </div>
        <span class="inline-flex w-fit items-center rounded-full border px-4 py-2 text-xs font-black tracking-widest {{ $tone['ring'] }} {{ $tone['text'] }}">
            {{ $level }} RISK
        </span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
        <div class="rounded-3xl border border-white/10 bg-slate-950/30 p-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <span class="text-6xl font-black tracking-tighter text-white">{{ $score }}</span>
                    <span class="text-xl font-bold text-slate-500">/100</span>
                </div>
                <x-heroicon-o-shield-check class="h-12 w-12 {{ $tone['text'] }}" />
            </div>
            <div class="mt-6 h-3 overflow-hidden rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-gradient-to-r {{ $tone['bar'] }} transition-all duration-700" style="width: {{ $score }}%"></div>
            </div>
            <div class="mt-3 flex justify-between text-[10px] font-bold uppercase tracking-widest text-slate-600">
                <span>Low</span><span>Medium</span><span>High</span>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($factors as $factor)
                @php $triggered = (bool) ($conditions[$factor['key']] ?? false); @endphp
                <div class="flex items-center justify-between gap-4 rounded-2xl border p-4 {{ $triggered ? 'border-rose-400/20 bg-rose-500/10' : 'border-white/10 bg-white/[.035]' }}">
                    <div class="flex items-center gap-3">
                        @if($triggered)
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6 flex-none text-rose-300" />
                        @else
                            <x-heroicon-o-check-circle class="h-6 w-6 flex-none text-emerald-300" />
                        @endif
                        <div>
                            <p class="text-sm font-bold text-white">{{ $factor['label'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $factor['value'] }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-black {{ $triggered ? 'text-rose-300' : 'text-slate-600' }}">{{ $triggered ? '+25' : '+0' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
