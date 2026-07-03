@props(['latest', 'analysis'])

@php
    $score = (int) ($analysis['score'] ?? $latest?->risk_score ?? 0);
    $riskLevel = $analysis['risk'] ?? $latest?->risk_level ?? 'Low';
    $triggeredRules = $analysis['triggered_rules'] ?? [];
    $triggeredIds = array_column($triggeredRules, 'id');
    $allRules = \App\Models\WeatherRule::where('is_active', true)->get();
    $maxScore = max((int) $allRules->sum('score_weight'), 100);
    $percentage = min(100, max(0, ($score / $maxScore) * 100));
    $radius = 54;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($percentage / 100 * $circumference);

    $tone = match ($riskLevel) {
        'Extreme' => ['text' => 'text-purple-300', 'stroke' => '#c084fc', 'bg' => 'bg-purple-500/10', 'border' => 'border-purple-400/30'],
        'High' => ['text' => 'text-rose-300', 'stroke' => '#fb7185', 'bg' => 'bg-rose-500/10', 'border' => 'border-rose-400/30'],
        'Medium' => ['text' => 'text-amber-300', 'stroke' => '#fbbf24', 'bg' => 'bg-amber-500/10', 'border' => 'border-amber-400/30'],
        default => ['text' => 'text-emerald-300', 'stroke' => '#4ade80', 'bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-400/30'],
    };

    $iconMap = [
        'temperature' => 'heroicon-o-sun',
        'humidity' => 'heroicon-o-beaker',
        'wind_speed' => 'heroicon-o-bars-3-bottom-left',
        'pressure' => 'heroicon-o-chart-bar',
        'visibility' => 'heroicon-o-eye',
        'uv_index' => 'heroicon-o-sun',
    ];

    $unitMap = [
        'temperature' => '°C',
        'humidity' => '%',
        'wind_speed' => ' m/s',
        'pressure' => ' hPa',
        'visibility' => ' km',
        'uv_index' => '',
    ];

    $insight = $analysis['insight'] ?? $latest?->insight ?? 'Current weather parameters are being monitored by the rule engine.';
@endphp

<section aria-labelledby="risk-analysis-title" class="glass-panel overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
                <x-heroicon-o-shield-check class="h-4 w-4" />
            </span>
            <h2 id="risk-analysis-title" class="text-base font-black text-white">Weather Risk Assessment</h2>
        </div>
        <span class="rounded-full border border-cyan-400/30 px-4 py-2 text-xs font-black text-slate-200">Rule Analysis</span>
    </div>

    <div class="grid gap-5 lg:grid-cols-[180px_1fr] xl:grid-cols-1 2xl:grid-cols-[180px_1fr]">
        <div class="flex flex-col items-center justify-center rounded-3xl border border-white/10 bg-slate-950/28 p-5 text-center">
            <div class="relative flex h-36 w-36 items-center justify-center">
                <svg class="absolute h-36 w-36 -rotate-90">
                    <circle cx="72" cy="72" r="{{ $radius }}" stroke="currentColor" stroke-width="11" fill="none" class="text-slate-800/90" />
                    <circle
                        cx="72"
                        cy="72"
                        r="{{ $radius }}"
                        stroke="{{ $tone['stroke'] }}"
                        stroke-width="11"
                        fill="none"
                        stroke-linecap="round"
                        style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $offset }};"
                    />
                </svg>
                <div class="relative">
                    <p class="text-4xl font-black text-white">{{ $score }}</p>
                    <p class="text-sm font-bold text-slate-300">/{{ $maxScore }}</p>
                </div>
            </div>
            <p class="mt-2 text-sm font-black uppercase {{ $tone['text'] }}">{{ $riskLevel }} Risk</p>
            <p class="mt-3 text-xs leading-5 text-slate-400">{{ $insight }}</p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-950/24 p-4">
            <div class="space-y-2.5">
                @forelse($allRules as $rule)
                    @php
                        $isTriggered = in_array($rule->id, $triggeredIds);
                        $currentValue = $latest ? $latest->getAttribute($rule->rule_type) : null;
                        $unit = $unitMap[$rule->rule_type] ?? '';
                        $icon = $iconMap[$rule->rule_type] ?? 'heroicon-o-exclamation-circle';
                    @endphp

                    <article class="grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/45 px-3 py-2.5 transition duration-300 hover:border-cyan-400/40">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/[.06] text-slate-300">
                            <x-dynamic-component :component="$icon" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-200">{{ $rule->name }}</p>
                            <p class="truncate text-xs {{ $isTriggered ? 'text-amber-300' : 'text-emerald-300' }}">
                                {{ $isTriggered ? 'Triggered' : 'Normal' }}
                                @if($currentValue !== null)
                                    &bull; {{ is_numeric($currentValue) ? number_format((float) $currentValue, 1) : $currentValue }}{{ $unit }}
                                @endif
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $isTriggered ? 'bg-amber-400/10 text-amber-300' : 'bg-emerald-400/10 text-emerald-300' }}">
                            +{{ $isTriggered ? $rule->score_weight : 0 }}
                        </span>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 py-8 text-center text-sm text-slate-500">
                        No active risk rules configured.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
