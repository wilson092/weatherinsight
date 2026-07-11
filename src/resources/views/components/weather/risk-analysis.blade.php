@props(['latest', 'analysis'])

@php
    // Use normalized values directly from the service
    $riskLevel = $analysis['risk_level'] ?? 'low';
    $triggeredRules = collect($analysis['triggered_rules'] ?? []);
    $allRules = $analysis['all_rules'] ?? collect();
    $weatherData = $analysis['weather_data'] ?? null;

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

    $labelMap = [
        'temperature' => 'Suhu',
        'humidity' => 'Kelembapan',
        'wind_speed' => 'Angin',
        'pressure' => 'Pressure',
        'visibility' => 'Visibility',
        'uv_index' => 'UV Index',
    ];

    // --- Group ALL active rules by type (no longer dropping duplicates) ---
    // Each type becomes ONE card. If more than one rule of that type is triggered,
    // all matching descriptions are shown, not just the first one.
    $rulesByType = $allRules->groupBy('rule_type');

    $cardsToDisplay = $rulesByType->map(function ($rules, $type) use ($triggeredRules, $labelMap) {
        $triggeredForType = $triggeredRules->where('rule_type', $type);
        $isTriggered = $triggeredForType->isNotEmpty();

        $statusText = 'Normal';
        if ($isTriggered && $type === 'pressure') {
            $first = $triggeredForType->first();
            $statusText = str_contains(strtolower($first->name), 'low') ? 'Low' : 'High';
        } elseif ($isTriggered) {
            $statusText = 'Triggered';
        }

        return [
            'rule_type' => $type,
            'name' => $labelMap[$type] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $type)),
            'is_triggered' => $isTriggered,
            'status_text' => $statusText,
            'descriptions' => $triggeredForType->pluck('description')->filter()->unique()->values(),
        ];
    })->sortBy('rule_type')->values();
@endphp

<section
    x-data="{
        unit: 'C',
        convertTemp(temp) {
            if (this.unit === 'F') {
                return (temp * 9/5) + 32;
            }
            return temp;
        }
    }"
    @temperature-unit-changed.window="unit = $event.detail"
    aria-labelledby="risk-analysis-title"
    class="glass-panel flex h-full flex-col overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6"
>
    <div class="mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
                <x-heroicon-o-shield-check class="h-4 w-4" />
            </span>
            <h2 id="risk-analysis-title" class="text-base font-black text-white">Weather Risk Assessment</h2>
        </div>
    </div>

    <div class="rounded-3xl border border-white/10 bg-slate-950/24 p-4">
        <div class="space-y-2.5">
            @forelse($cardsToDisplay as $card)
                @php
                    $currentValue = $weatherData ? $weatherData->getAttribute($card['rule_type']) : null;
                    $unit = $unitMap[$card['rule_type']] ?? '';
                    $icon = $iconMap[$card['rule_type']] ?? 'heroicon-o-exclamation-circle';
                    $isTriggered = $card['is_triggered'];
                @endphp

                <article class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-slate-900/45 px-3.5 py-3 transition duration-300 hover:border-cyan-400/40">
                    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/[.06] text-slate-300">
                            <x-dynamic-component :component="$icon" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-200">{{ $card['name'] }}</p>
                            <p class="truncate text-xs {{ $isTriggered ? 'text-amber-300' : 'text-emerald-300' }}">
                                {{ $card['status_text'] }}
                                @if ($currentValue !== null)
                                    &bull;
                                    <span
                                        x-text="unit === 'F' && '{{ $card['rule_type'] }}' === 'temperature' ? convertTemp({{ $currentValue }}).toFixed(1) : {{ is_numeric($currentValue) ? number_format((float) $currentValue, 1, '.', '') : "'$currentValue'" }}"></span>{{ $unit }}
                                @endif
                            </p>
                        </div>
                        @if ($isTriggered)
                            <span class="rounded-full px-2.5 py-1 text-xs font-black bg-amber-400/10 text-amber-300">
                                Terpicu
                            </span>
                        @else
                            <span class="rounded-full px-2.5 py-1 text-xs font-black bg-emerald-400/10 text-emerald-300">
                                Aman
                            </span>
                        @endif
                    </div>
                    @if ($isTriggered && $card['descriptions']->isNotEmpty())
                        <div class="space-y-1.5 border-t border-slate-800 pt-2.5 text-xs text-slate-400">
                            @foreach ($card['descriptions'] as $description)
                                <p>{{ $description }}</p>
                            @endforeach
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-white/10 py-8 text-center text-sm text-slate-500">
                    No active risk rules configured.
                </div>
            @endforelse
        </div>
    </div>
</section>