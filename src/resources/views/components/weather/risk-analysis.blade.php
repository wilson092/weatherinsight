@props(['latest', 'analysis'])

@php
    $score = (int) ($analysis['score'] ?? 0);
    $riskLevel = $analysis['risk'] ?? 'Low';
    $triggeredRules = $analysis['triggered_rules'] ?? [];
    $allRules = \App\Models\WeatherRule::where('is_active', true)->get();
    $riskCategory = \App\Models\RiskCategory::where('name', $riskLevel)->first();
    $maxScore = $allRules->sum('score_weight');
    $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
    $triggeredCount = count($triggeredRules);
    $totalRules = $allRules->count();

    // Color schemes based on risk level
    $colorScheme = match ($riskCategory?->risk_level) {
        'extreme' => [
            'text' => 'text-purple-300',
            'gradient' => 'from-purple-500 to-pink-500',
            'ring' => 'border-purple-400/30 bg-purple-500/10',
            'glow' => 'shadow-purple-500/20',
            'progress' => '#a855f7', // purple-500
        ],
        'high' => [
            'text' => 'text-red-300',
            'gradient' => 'from-red-500 to-rose-500',
            'ring' => 'border-red-400/30 bg-red-500/10',
            'glow' => 'shadow-red-500/20',
            'progress' => '#ef4444', // red-500
        ],
        'medium' => [
            'text' => 'text-amber-300',
            'gradient' => 'from-amber-400 to-yellow-400',
            'ring' => 'border-amber-400/30 bg-amber-500/10',
            'glow' => 'shadow-amber-500/20',
            'progress' => '#f59e0b', // amber-500
        ],
        default => [
            'text' => 'text-emerald-300',
            'gradient' => 'from-emerald-400 to-teal-400',
            'ring' => 'border-emerald-400/30 bg-emerald-500/10',
            'glow' => 'shadow-emerald-500/20',
            'progress' => '#10b981', // emerald-500
        ],
    };

    // Risk descriptions
    $riskDescriptions = [
        'Low' => 'Weather conditions are generally safe today. Outdoor activities are recommended with standard precautions.',
        'Medium' => 'Some weather factors require attention. Monitor conditions and take appropriate precautions for outdoor activities.',
        'High' => 'Adverse weather conditions detected. Exercise caution and limit outdoor exposure. Stay informed of updates.',
        'Extreme' => 'Dangerous weather conditions present. Avoid outdoor exposure and seek shelter. Follow safety protocols.',
    ];
    
    $riskDescription = $riskDescriptions[$riskLevel] ?? $riskDescriptions['Low'];
    
    // SVG circle calculations
    $radius = 85;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($percentage / 100 * $circumference);
    
    // Icon mapping for risk factors
    $iconMap = [
        'temperature' => 'heroicon-o-fire',
        'humidity' => 'heroicon-o-beaker',
        'wind_speed' => 'heroicon-o-arrow-trending-up',
        'pressure' => 'heroicon-o-chart-bar',
        'visibility' => 'heroicon-o-eye',
        'uv_index' => 'heroicon-o-sun',
    ];
@endphp

<section aria-labelledby="risk-analysis-title" class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8">
    <!-- Background decorations -->
    <div class="absolute -right-20 top-0 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/4 h-56 w-56 rounded-full bg-teal-400/10 blur-3xl"></div>

    <div class="relative">
        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Safety Overview</p>
                <h2 id="risk-analysis-title" class="mt-2 text-3xl font-black text-white sm:text-4xl">Weather Risk Assessment</h2>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-cyan-300/20 bg-gradient-to-br from-cyan-400/20 to-teal-400/10 text-cyan-200 shadow-lg shadow-cyan-950/30">
                <x-heroicon-o-shield-check class="h-7 w-7" />
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid gap-6 lg:grid-cols-[400px_1fr]">
            <!-- Risk Score Card with Circular Progress -->
            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-950/60 to-slate-900/40 p-8 backdrop-blur-sm">
                <!-- Circular Progress -->
                <div class="relative mx-auto mb-6 flex h-52 w-52 items-center justify-center">
                    <!-- Background Circle -->
                    <svg class="absolute h-52 w-52 -rotate-90 transform">
                        <circle
                            cx="104"
                            cy="104"
                            r="{{ $radius }}"
                            stroke="currentColor"
                            stroke-width="12"
                            fill="none"
                            class="text-slate-800"
                        />
                        <!-- Progress Circle -->
                        <circle
                            cx="104"
                            cy="104"
                            r="{{ $radius }}"
                            stroke="{{ $colorScheme['progress'] }}"
                            stroke-width="12"
                            fill="none"
                            stroke-linecap="round"
                            style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $offset }}; transition: stroke-dashoffset 1s ease-in-out;"
                            class="drop-shadow-[0_0_8px_{{ $colorScheme['progress'] }}]"
                        />
                    </svg>
                    
                    <!-- Center Content -->
                    <div class="z-10 text-center">
                        <div class="flex items-end justify-center gap-1">
                            <span class="text-5xl font-black tracking-tighter text-white">{{ $score }}</span>
                            <span class="mb-1 text-lg font-bold text-slate-500">/{{ $maxScore }}</span>
                        </div>
                        <p class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-500">Risk Score</p>
                    </div>
                </div>

                <!-- Risk Level Badge -->
                <div class="mb-4 flex justify-center">
                    <span class="inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-black uppercase tracking-widest {{ $colorScheme['ring'] }} {{ $colorScheme['text'] }} shadow-lg {{ $colorScheme['glow'] }}">
                        <x-heroicon-o-shield-exclamation class="h-4 w-4" />
                        {{ $riskLevel }} Risk
                    </span>
                </div>

                <!-- Risk Description -->
                <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                    <p class="text-center text-sm leading-relaxed text-slate-300">
                        {{ $riskDescription }}
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="mt-4 flex items-center justify-center gap-4 text-xs">
                    <div class="text-center">
                        <p class="font-black text-white">{{ number_format($percentage, 0) }}%</p>
                        <p class="mt-1 text-slate-500">Risk Level</p>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                    <div class="text-center">
                        <p class="font-black {{ $triggeredCount > 0 ? $colorScheme['text'] : 'text-emerald-300' }}">{{ $triggeredCount }}/{{ $totalRules }}</p>
                        <p class="mt-1 text-slate-500">Factors Active</p>
                    </div>
                </div>
            </div>

            <!-- Risk Factors Grid -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">Risk Factors Analysis</h3>
                
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
                                'visibility' => 'km',
                                'uv_index' => '',
                                default => ''
                            };
                            $icon = $iconMap[$rule->rule_type] ?? 'heroicon-o-exclamation-circle';
                        @endphp
                        
                        <article class="group relative overflow-hidden rounded-2xl border p-4 transition-all duration-300 {{ $isTriggered ? 'border-rose-400/30 bg-gradient-to-br from-rose-500/10 to-rose-600/5 hover:border-rose-400/50 hover:shadow-lg hover:shadow-rose-500/10' : 'border-white/10 bg-gradient-to-br from-white/[.04] to-white/[.02] hover:border-emerald-300/30 hover:bg-white/[.06]' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <!-- Icon -->
                                    <div class="flex h-10 w-10 flex-none items-center justify-center rounded-xl {{ $isTriggered ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/10 text-emerald-300' }} transition-colors">
                                        <x-dynamic-component :component="$icon" class="h-5 w-5" />
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-white">{{ $rule->name }}</p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $rule->kondisi }}
                                        </p>
                                        <p class="mt-1 text-xs font-semibold {{ $isTriggered ? 'text-rose-300' : 'text-emerald-300' }}">
                                            Current: {{ $currentValue ? number_format($currentValue, 1) : 'N/A' }}{{ $unit }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Score Badge -->
                                <div class="flex-none">
                                    @if($isTriggered)
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-500/20 text-xs font-black text-rose-300">
                                            +{{ $rule->score_weight }}
                                        </span>
                                    @else
                                        <x-heroicon-o-check-circle class="h-6 w-6 text-emerald-300/60" />
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Weather Insight Card -->
        <div class="mt-6 rounded-3xl border border-cyan-300/20 bg-gradient-to-br from-cyan-400/5 to-teal-400/5 p-6 backdrop-blur-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400/20 to-teal-400/10 text-cyan-300">
                    <x-heroicon-o-light-bulb class="h-6 w-6" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-white">Weather Insight</h3>
                    <p class="mt-2 leading-relaxed text-slate-300">
                        Based on current weather conditions, today presents a <strong class="{{ $colorScheme['text'] }}">{{ strtoupper($riskLevel) }}</strong> risk level for outdoor activities. 
                        @if($triggeredCount > 0)
                            {{ $triggeredCount }} of {{ $totalRules }} risk factors are currently active and require attention.
                        @else
                            All weather parameters are within safe ranges. Conditions are favorable for outdoor plans.
                        @endif
                    </p>
                    
                    @if($triggeredCount > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($triggeredRules as $triggered)
                                @php
                                    $triggeredRule = $allRules->firstWhere('id', $triggered['id']);
                                @endphp
                                @if($triggeredRule)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-300">
                                        <x-heroicon-o-exclamation-triangle class="h-3 w-3" />
                                        {{ $triggeredRule->name }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>