@props(['latest', 'analysis'])

@php
    $score = (int) ($analysis['score'] ?? 0);
    $riskLevel = $analysis['risk'] ?? 'Low';
    $triggeredRules = $analysis['triggered_rules'] ?? [];
    $allRules = \App\Models\WeatherRule::where('is_active', true)->get();
    $riskCategory = \App\Models\RiskCategory::where('name', $riskLevel)->first();

    $tone = match ($riskCategory?->risk_level) {
        'high' => ['text' => 'text-red-300', 'bar' => 'from-red-500 to-red-400', 'ring' => 'border-red-400/25 bg-red-500/10'],
        'medium' => ['text' => 'text-amber-300', 'bar' => 'from-amber-400 to-yellow-300', 'ring' => 'border-amber-400/25 bg-amber-500/10'],
        default => ['text' => 'text-emerald-300', 'bar' => 'from-emerald-400 to-teal-300', 'ring' => 'border-emerald-400/25 bg-emerald-500/10'],
    };
@endphp

<section aria-labelledby="risk-analysis-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Analisis Risiko</p>
            <h2 id="risk-analysis-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Skor Risiko Cuaca</h2>
        </div>
        <span class="inline-flex w-fit items-center rounded-full border px-4 py-2 text-xs font-black tracking-widest {{ $tone['ring'] }} {{ $tone['text'] }}">
            RISIKO {{ strtoupper($riskLevel) }}
        </span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
        <div class="rounded-3xl border border-white/10 bg-slate-950/30 p-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <span class="text-6xl font-black tracking-tighter text-white">{{ $score }}</span>
                    <span class="text-xl font-bold text-slate-500">/{{ $allRules->sum('score_weight') }}</span>
                </div>
                <x-heroicon-o-shield-check class="h-12 w-12 {{ $tone['text'] }}" />
            </div>
            <div class="mt-6 h-3 overflow-hidden rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-gradient-to-r {{ $tone['bar'] }} transition-all duration-700" style="width: {{ $score > 0 ? ($score / $allRules->sum('score_weight')) * 100 : 0 }}%"></div>
            </div>
            <div class="mt-3 flex justify-between text-[10px] font-bold uppercase tracking-widest text-slate-600">
                <span>Rendah</span><span>Sedang</span><span>Tinggi</span>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($allRules as $rule)
                @php
                    $isTriggered = in_array($rule->id, array_column($triggeredRules, 'id'));
                    $currentValue = $latest ? $latest->{$rule->rule_type} : null;
                    $unit = match($rule->rule_type) {
                        'temperature' => '°C',
                        'humidity' => '%',
                        'wind_speed' => 'm/s',
                        'pressure' => 'hPa',
                        default => ''
                    };
                @endphp
                <div class="flex items-center justify-between gap-4 rounded-2xl border p-4 {{ $isTriggered ? 'border-rose-400/20 bg-rose-500/10' : 'border-white/10 bg-white/[.035]' }}">
                    <div class="flex items-center gap-3">
                        @if($isTriggered)
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6 flex-none text-rose-300" />
                        @else
                            <x-heroicon-o-check-circle class="h-6 w-6 flex-none text-emerald-300" />
                        @endif
                        <div>
                            <p class="text-sm font-bold text-white">{{ $rule->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $rule->kondisi }} (Saat ini: {{ $currentValue ? number_format($currentValue, 1) : 'N/A' }}{{ $unit }})
                            </p>
                        </div>
                    </div>
                    <span class="text-sm font-black {{ $isTriggered ? 'text-rose-300' : 'text-slate-600' }}">{{ $isTriggered ? '+'.$rule->score_weight : '+0' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
