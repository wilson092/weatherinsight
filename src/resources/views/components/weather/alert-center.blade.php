@props(['alerts', 'riskAnalysis', 'latest' => null])

@php
    $triggeredRules = collect($riskAnalysis['triggered_rules'] ?? []);
    $hasAlerts = $triggeredRules->isNotEmpty() || count($alerts) > 0;
    $recommendation = trim($riskAnalysis['recommendation'] ?? $latest?->recommendation ?? 'Monitor weather conditions and stay informed of any changes.');
    $insight = $riskAnalysis['insight'] ?? $latest?->insight ?? 'Current conditions are within normal parameters.';
    $riskCategory = $riskAnalysis['risk_category'] ?? $latest?->risk_category;
    $riskLevel = $riskAnalysis['risk_level'] ?? $latest?->risk_level ?? 'low';
    $riskName = $riskAnalysis['risk'] ?? $riskCategory?->name ?? ucfirst($riskLevel).' Risk';
    $riskKey = match (true) {
        str_contains(strtolower($riskLevel), 'extreme') => 'Extreme',
        str_contains(strtolower($riskLevel), 'high') => 'High',
        str_contains(strtolower($riskLevel), 'medium') => 'Medium',
        default => 'Low',
    };
    $updatedAt = $latest?->recorded_at ?? now();

    $riskColors = [
        'Low' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300',
        'Medium' => 'border-yellow-400/30 bg-yellow-500/10 text-yellow-300',
        'High' => 'border-orange-400/30 bg-orange-500/10 text-orange-300',
        'Extreme' => 'border-red-400/30 bg-red-500/10 text-red-300',
    ];

    $unitMap = [
        'temperature' => '°C',
        'humidity' => '%',
        'wind_speed' => ' m/s',
        'pressure' => ' hPa',
        'visibility' => ' km',
        'uv_index' => '',
    ];

    $iconMap = [
        'temperature' => 'heroicon-o-fire',
        'humidity' => 'heroicon-o-beaker',
        'wind_speed' => 'heroicon-o-bars-3-bottom-left',
        'pressure' => 'heroicon-o-chart-bar',
        'visibility' => 'heroicon-o-eye',
        'uv_index' => 'heroicon-o-sun',
    ];

    $formatValue = function ($value, string $unit = '') {
        if ($value === null || $value === '') {
            return 'Unavailable';
        }

        return (is_numeric($value) ? number_format((float) $value, 1) : $value).$unit;
    };

    $formatThreshold = function ($rule, string $unit = '') use ($formatValue) {
        if ($rule->operator === 'between') {
            return $formatValue($rule->min_value, $unit).' - '.$formatValue($rule->max_value, $unit);
        }

        return trim($rule->operator.' '.$formatValue($rule->threshold_value, $unit));
    };

    $recommendedActions = collect(preg_split('/[\r\n.;]+/', $recommendation))
        ->map(fn ($item) => trim($item, " \t\n\r\0\x0B-*"))
        ->filter()
        ->take(4);
@endphp

<section
    x-data="{
        unit: 'C',
        convertTemp(temp) {
            if (this.unit === 'F') {
                return (temp * 9/5) + 32;
            }
            return temp;
        },
        formatValue(value, unit, type) {
            if (value === null || value === '') {
                return 'Unavailable';
            }
            
            let temp = value;
            if (type === 'temperature') {
                temp = this.convertTemp(value);
            }

            return (isNaN(temp) ? temp : parseFloat(temp).toFixed(1)) + (unit === '°C' && this.unit === 'F' ? '°F' : unit);
        }
    }"
    @temperature-unit-changed.window="unit = $event.detail"
    class="grid h-full gap-5 md:grid-cols-2 xl:grid-cols-1"
>
    <article aria-labelledby="alert-center-title" class="glass-panel flex min-h-[230px] flex-col overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
                    <x-heroicon-o-shield-check class="h-4 w-4" />
                </span>
                <h2 id="alert-center-title" class="text-base font-black text-white">Weather Alert Center</h2>
            </div>
            <span class="rounded-full border px-3 py-1 text-xs font-black uppercase {{ $riskColors[$riskKey] }}">
                {{ $riskName }}
            </span>
        </div>

        @if(!$hasAlerts)
            <div class="flex flex-1 flex-col justify-center text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border-2 border-emerald-400/30 bg-emerald-500/10 text-emerald-300">
                    <x-heroicon-o-check-circle class="h-7 w-7" />
                </div>
                <h3 class="mt-4 text-lg font-black text-emerald-200">No Active Weather Alerts</h3>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                    All monitored weather parameters are within configured thresholds.
                </p>
                <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3">
                    <p class="text-xs font-bold uppercase text-slate-500">Last Updated</p>
                    <p class="mt-1 text-sm font-black text-white">{{ $updatedAt->format('d M Y, H:i') }}</p>
                </div>
            </div>
        @else
            <div class="flex-1 space-y-3 overflow-y-auto pr-1">
                @foreach($triggeredRules as $rule)
                    @php
                        $unit = $unitMap[$rule->rule_type] ?? '';
                        $currentValue = $latest ? $latest->getAttribute($rule->rule_type) : null;
                        $severity = $riskLevel;
                        $alertStyle = $riskColors[$riskKey];
                        $icon = $iconMap[$rule->rule_type] ?? 'heroicon-o-exclamation-triangle';
                    @endphp

                    <div class="rounded-2xl border p-4 {{ $alertStyle }}">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-white/[.08]">
                                <x-dynamic-component :component="$icon" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-black text-white">{{ $rule->name }}</h3>
                                    <span class="rounded-full bg-white/[.08] px-2 py-0.5 text-[10px] font-black uppercase tracking-wide">{{ $severity }}</span>
                                </div>
                                <dl class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                                    <div>
                                        <dt class="text-slate-400">Current Value</dt>
                                        <dd class="mt-0.5 font-bold text-white" x-text="formatValue({{ $currentValue ?? 'null' }}, '{{ $unit }}', '{{ $rule->rule_type }}')"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-400">Threshold</dt>
                                        <dd class="mt-0.5 font-bold text-white">{{ $formatThreshold($rule, $unit) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-400">Severity</dt>
                                        <dd class="mt-0.5 font-bold text-white">{{ $severity }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-slate-400">Trigger Time</dt>
                                        <dd class="mt-0.5 font-bold text-white">{{ $updatedAt->format('H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($triggeredRules->isEmpty())
                    @foreach($alerts as $alert)
                        <div class="rounded-2xl border p-4 {{ $riskColors[$riskKey] }}">
                            <div class="flex items-start gap-3">
                                <span class="flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-white/[.08]">
                                    <x-dynamic-component :component="$alert['icon']" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-black text-white">{{ $alert['title'] }}</h3>
                                        <span class="rounded-full bg-white/[.08] px-2 py-0.5 text-[10px] font-black uppercase tracking-wide">{{ $alert['level'] ?? $riskLevel }}</span>
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">{{ $alert['message'] }}</p>
                                    <dl class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                                        <div>
                                            <dt class="text-slate-400">Severity</dt>
                                            <dd class="mt-0.5 font-bold text-white">{{ $alert['level'] ?? $riskLevel }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-slate-400">Trigger Time</dt>
                                            <dd class="mt-0.5 font-bold text-white">{{ $updatedAt->format('H:i') }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </article>

    <article aria-labelledby="recommendation-title" class="glass-panel flex min-h-[230px] flex-col overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6">
        <div class="mb-4 flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full border border-amber-400/30 bg-amber-400/10 text-amber-300">
                <x-heroicon-o-light-bulb class="h-4 w-4" />
            </span>
            <h2 id="recommendation-title" class="text-base font-black text-white">Weather Recommendation</h2>
        </div>

        <div class="flex flex-1 flex-col justify-between rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900/70 to-teal-950/35 p-5">
            <div>
                <p class="text-base font-black text-emerald-300">{{ $recommendation }}</p>
                <p class="mt-4 text-sm leading-6 text-slate-300">{{ $insight }}</p>
            </div>

            @if($recommendedActions->isNotEmpty())
                <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <h3 class="text-sm font-black text-white">Recommended Actions</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach($recommendedActions as $action)
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <x-heroicon-o-check-circle class="mt-0.5 h-4 w-4 flex-none text-cyan-300" />
                                <span>{{ $action }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-5 flex items-center justify-between gap-3">
                <span class="text-xs font-bold uppercase text-slate-500">Current Risk</span>
                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-black uppercase {{ $riskColors[$riskKey] }}">
                    <x-heroicon-o-shield-exclamation class="h-3.5 w-3.5" />
                    {{ $riskName }}
                </span>
            </div>
        </div>
    </article>
</section>
