@props(['riskAnalysis', 'latest' => null])

@php
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

    $riskColors = [
        'Low' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300',
        'Medium' => 'border-yellow-400/30 bg-yellow-500/10 text-yellow-300',
        'High' => 'border-orange-400/30 bg-orange-500/10 text-orange-300',
        'Extreme' => 'border-red-400/30 bg-red-500/10 text-red-300',
    ];

    $recommendedActions = collect(preg_split('/[\r\n.;]+/', $recommendation))
        ->map(fn ($item) => trim($item, " \t\n\r\0\x0B-*"))
        ->filter()
        ->take(4);
@endphp

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
                {{ $riskLevel }}
            </span>
        </div>
    </div>
</article>