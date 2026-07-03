@props(['alerts', 'riskAnalysis'])

@php
    $alertCount = count($alerts);
    $hasAlerts = $alertCount > 0;
    
    // Get recommendation and insight from risk analysis
    $recommendation = $riskAnalysis['recommendation'] ?? 'Monitor weather conditions and stay informed of any changes.';
    $insight = $riskAnalysis['insight'] ?? 'Current conditions are within normal parameters.';
    $riskLevel = $riskAnalysis['risk'] ?? 'Low';
    
    // Color scheme for risk levels
    $riskColors = [
        'Low' => 'text-emerald-300 bg-emerald-500/10 border-emerald-400/20',
        'Medium' => 'text-amber-300 bg-amber-500/10 border-amber-400/20',
        'High' => 'text-red-300 bg-red-500/10 border-red-400/20',
        'Extreme' => 'text-purple-300 bg-purple-500/10 border-purple-400/20',
    ];
@endphp

<section aria-labelledby="alert-center-title" class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8">
    <!-- Background decorations -->
    <div class="absolute -right-16 top-0 h-56 w-56 rounded-full bg-rose-400/10 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/4 h-48 w-48 rounded-full bg-amber-400/10 blur-3xl"></div>

    <div class="relative">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Automated Monitoring</p>
                <h2 id="alert-center-title" class="mt-2 text-3xl font-black text-white sm:text-4xl">Weather Alert Center</h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-sm font-black {{ $hasAlerts ? 'text-rose-300' : 'text-emerald-300' }}">
                    {{ $alertCount }}
                </span>
                <span class="text-sm text-slate-400">active {{ $alertCount === 1 ? 'alert' : 'alerts' }}</span>
            </div>
        </div>

        <!-- Alerts Section -->
        @if(!$hasAlerts)
            <!-- No Active Alerts -->
            <div class="mb-6 overflow-hidden rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-emerald-500/10 to-teal-500/5 p-8 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                    <div class="flex h-16 w-16 flex-none items-center justify-center rounded-2xl bg-emerald-500/20">
                        <x-heroicon-o-shield-check class="h-9 w-9 text-emerald-300 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-black text-emerald-200">No Active Weather Alerts</h3>
                        <p class="mt-2 leading-relaxed text-emerald-100/70">
                            Current weather conditions are within safe thresholds. All monitored parameters are operating normally.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- Active Alerts Grid -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2">
                @foreach($alerts as $alert)
                    @php
                        $level = $alert['level'] ?? 'MEDIUM';
                        $alertStyle = match($level) {
                            'EXTREME' => [
                                'border' => 'border-purple-400/30',
                                'bg' => 'bg-gradient-to-br from-purple-500/10 to-pink-500/5',
                                'text' => 'text-purple-300',
                                'badge' => 'bg-purple-500/20 text-purple-200',
                                'glow' => 'shadow-purple-500/10',
                            ],
                            'HIGH' => [
                                'border' => 'border-rose-400/30',
                                'bg' => 'bg-gradient-to-br from-rose-500/10 to-red-500/5',
                                'text' => 'text-rose-300',
                                'badge' => 'bg-rose-500/20 text-rose-200',
                                'glow' => 'shadow-rose-500/10',
                            ],
                            'MEDIUM' => [
                                'border' => 'border-amber-400/30',
                                'bg' => 'bg-gradient-to-br from-amber-500/10 to-yellow-500/5',
                                'text' => 'text-amber-300',
                                'badge' => 'bg-amber-500/20 text-amber-200',
                                'glow' => 'shadow-amber-500/10',
                            ],
                            default => [
                                'border' => 'border-emerald-400/30',
                                'bg' => 'bg-gradient-to-br from-emerald-500/10 to-teal-500/5',
                                'text' => 'text-emerald-300',
                                'badge' => 'bg-emerald-500/20 text-emerald-200',
                                'glow' => 'shadow-emerald-500/10',
                            ],
                        };
                    @endphp
                    
                    <article class="group rounded-3xl border p-6 backdrop-blur-sm transition-all duration-300 {{ $alertStyle['border'] }} {{ $alertStyle['bg'] }} hover:{{ $alertStyle['border'] }} hover:shadow-lg {{ $alertStyle['glow'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl {{ $alertStyle['badge'] }}">
                                <x-dynamic-component :component="$alert['icon']" class="h-6 w-6 drop-shadow-sm" />
                            </div>
                            
                            <!-- Content -->
                            <div class="min-w-0 flex-1">
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    <h3 class="font-black text-white">{{ $alert['title'] }}</h3>
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-widest {{ $alertStyle['badge'] }}">
                                        {{ $level }}
                                    </span>
                                </div>
                                <p class="text-sm leading-relaxed text-slate-300">
                                    {{ $alert['message'] }}
                                </p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <!-- Weather Recommendation Card -->
        <div class="rounded-3xl border border-cyan-300/20 bg-gradient-to-br from-cyan-400/5 to-teal-400/5 p-6 backdrop-blur-sm">
            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400/20 to-teal-400/10 text-cyan-300">
                    <x-heroicon-o-light-bulb class="h-6 w-6" />
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                    <h3 class="text-lg font-black text-white">Weather Recommendation</h3>
                    <p class="mt-2 leading-relaxed text-slate-300">
                        {{ $recommendation }}
                    </p>
                    
                    <!-- Insight Section -->
                    @if($insight && $insight !== 'No specific insight available.')
                        <div class="mt-4 rounded-2xl border border-white/10 bg-white/[.03] p-4">
                            <div class="flex items-start gap-2">
                                <x-heroicon-o-information-circle class="h-5 w-5 flex-none text-cyan-300" />
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-300">Insight</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $insight }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Risk Level Indicator -->
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Current Risk:</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $riskColors[$riskLevel] ?? $riskColors['Low'] }}">
                            <x-heroicon-o-shield-exclamation class="h-3.5 w-3.5" />
                            {{ $riskLevel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>