@props(['latest', 'analysis'])

@php
    // Use normalized values directly from the service
    $riskLevel = $analysis['risk_level'] ?? 'low';
    $triggeredRules = $analysis['triggered_rules'] ?? [];
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

    // --- Logic to consolidate pressure rules ---
    $rulesToDisplay = [];
    $pressureRules = $allRules->where('rule_type', 'pressure');
    $otherRules = $allRules->where('rule_type', '!=', 'pressure')->groupBy('rule_type');

    // Add non-pressure rules (one per type)
    foreach ($otherRules as $type => $rules) {
        $rulesToDisplay[] = $rules->first();
    }

    // Consolidate and add the pressure rule
    if ($pressureRules->isNotEmpty()) {
        $pressureTriggeredRule = collect($triggeredRules)->where('rule_type', 'pressure')->first();
        $status = 'Normal';
        $scoreContribution = 0;

        if ($pressureTriggeredRule) {
            $status = str_contains(strtolower($pressureTriggeredRule->name), 'low') ? 'Low' : 'High';
            $scoreContribution = $pressureTriggeredRule->score_weight;
        }

        // Create a representative rule for display
        $representativePressureRule = $pressureRules->first();
        $representativePressureRule->name = 'Pressure'; // Consolidated name
        $representativePressureRule->status = $status;
        $representativePressureRule->score_contribution = $scoreContribution;
        $rulesToDisplay[] = $representativePressureRule;
    }
    // Sort rules for consistent order
    $rulesToDisplay = collect($rulesToDisplay)->sortBy('rule_type')->values();
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
            @forelse($rulesToDisplay as $rule)
                @php
                    $currentValue = $weatherData ? $weatherData->getAttribute($rule->rule_type) : null;
                    $unit = $unitMap[$rule->rule_type] ?? '';
                    $icon = $iconMap[$rule->rule_type] ?? 'heroicon-o-exclamation-circle';

                    if ($rule->rule_type === 'pressure') {
                        $isTriggered = $rule->status !== 'Normal';
                        $statusText = $rule->status;
                        $scoreContribution = $rule->score_contribution;
                    } else {
                        $triggeredRule = collect($triggeredRules)->where('id', $rule->id)->first();
                        $isTriggered = (bool)$triggeredRule;
                        $statusText = $isTriggered ? 'Triggered' : 'Normal';
                        $scoreContribution = $isTriggered ? $triggeredRule->score_weight : 0;
                    }
                @endphp

                <article class="grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/45 px-3 py-2.5 transition duration-300 hover:border-cyan-400/40">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/[.06] text-slate-300">
                        <x-dynamic-component :component="$icon" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-200">{{ $rule->name }}</p>
                        <p class="truncate text-xs {{ $isTriggered ? 'text-amber-300' : 'text-emerald-300' }}">
                            {{ $statusText }}
                            @if($currentValue !== null)
                                &bull;
                                <span x-text="unit === 'F' && '{{ $rule->rule_type }}' === 'temperature' ? convertTemp({{ $currentValue }}).toFixed(1) : {{ is_numeric($currentValue) ? number_format((float) $currentValue, 1) : "'$currentValue'" }}"></span>{{ $unit }}
                            @endif
                        </p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $isTriggered ? 'bg-amber-400/10 text-amber-300' : 'bg-emerald-400/10 text-emerald-300' }}">
                        +{{ $scoreContribution }}
                    </span>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-white/10 py-8 text-center text-sm text-slate-500">
                    No active risk rules configured.
                </div>
            @endforelse
        </div>
    </div>
</section>