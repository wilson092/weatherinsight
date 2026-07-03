@props(['leaderboards'])

@php
    $rankedCities = collect($leaderboards)
        ->flatMap(fn ($items) => $items)
        ->unique('city')
        ->sortByDesc(fn ($weather) => (int) ($weather->risk_score ?? 0))
        ->values();

    $riskColors = [
        'Low' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300',
        'Medium' => 'border-yellow-400/30 bg-yellow-500/10 text-yellow-300',
        'High' => 'border-orange-400/30 bg-orange-500/10 text-orange-300',
        'Extreme' => 'border-red-400/30 bg-red-500/10 text-red-300',
    ];
@endphp

<section id="leaderboard" aria-labelledby="leaderboard-title" class="glass-panel overflow-hidden rounded-3xl">
    <div class="border-b border-white/10 p-6 sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Latest City Snapshots</p>
                <h2 id="leaderboard-title" class="mt-2 text-3xl font-black text-white sm:text-4xl">Extreme Weather Leaderboard</h2>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3">
                    <p class="text-xs text-slate-500">Ranked</p>
                    <p class="mt-1 text-xl font-black text-white">{{ $rankedCities->count() }}</p>
                </div>
                <div class="rounded-2xl border border-orange-400/20 bg-orange-500/10 px-4 py-3">
                    <p class="text-xs text-orange-200/70">Hottest</p>
                    <p class="mt-1 text-xl font-black text-orange-200">{{ optional($leaderboards['hottest']->first())->city ?? '--' }}</p>
                </div>
                <div class="rounded-2xl border border-sky-400/20 bg-sky-500/10 px-4 py-3">
                    <p class="text-xs text-sky-200/70">Humid</p>
                    <p class="mt-1 text-xl font-black text-sky-200">{{ optional($leaderboards['humid']->first())->city ?? '--' }}</p>
                </div>
                <div class="rounded-2xl border border-teal-400/20 bg-teal-500/10 px-4 py-3">
                    <p class="text-xs text-teal-200/70">Wind</p>
                    <p class="mt-1 text-xl font-black text-teal-200">{{ optional($leaderboards['wind']->first())->city ?? '--' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px]">
            <thead class="border-b border-white/10 bg-slate-950/50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-400">Rank</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-400">City</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-400">Temperature</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-400">Humidity</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-400">Wind</th>
                    <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-400">Risk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($rankedCities as $rank => $weather)
                    @php
                        $riskLevel = $weather->risk_level ?? 'low';
                        $riskCategory = $weather->risk_category;
                        $riskName = $riskCategory?->name ?? ucfirst($riskLevel).' Risk';
                        $riskKey = match (true) {
                            str_contains(strtolower($riskLevel), 'extreme') => 'Extreme',
                            str_contains(strtolower($riskLevel), 'high') => 'High',
                            str_contains(strtolower($riskLevel), 'medium') => 'Medium',
                            default => 'Low',
                        };
                    @endphp
                    <tr class="transition hover:bg-white/[.04]">
                        <td class="px-6 py-5">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $rank === 0 ? 'bg-amber-300 text-amber-950' : 'bg-slate-900 text-slate-300' }} text-sm font-black">
                                #{{ $rank + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                @if($weather->weather_icon)
                                    <img class="h-10 w-10" src="https://openweathermap.org/img/wn/{{ $weather->weather_icon }}@2x.png" alt="{{ $weather->weather_description ?? 'Weather icon' }}">
                                @endif
                                <div>
                                    <p class="font-black text-white">{{ $weather->city }}</p>
                                    <p class="text-xs capitalize text-slate-500">{{ $weather->weather_description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right text-lg font-black text-white">{{ number_format($weather->temperature, 1) }}°C</td>
                        <td class="px-6 py-5 text-right text-lg font-black text-white">{{ number_format($weather->humidity, 0) }}%</td>
                        <td class="px-6 py-5 text-right text-lg font-black text-white">{{ number_format($weather->wind_speed, 1) }} m/s</td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black uppercase {{ $riskColors[$riskKey] }}">
                                {{ $riskName }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center text-sm text-slate-500">No city data yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
