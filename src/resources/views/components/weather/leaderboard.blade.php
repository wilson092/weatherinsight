@props(['leaderboards'])

@php
    $boards = [
        ['key' => 'hottest', 'title' => 'Hottest Cities', 'field' => 'temperature', 'unit' => '°C', 'icon' => 'heroicon-o-fire', 'accent' => 'text-orange-300 bg-orange-500/10 border-orange-400/20'],
        ['key' => 'humid', 'title' => 'Most Humid Cities', 'field' => 'humidity', 'unit' => '%', 'icon' => 'heroicon-o-beaker', 'accent' => 'text-sky-300 bg-sky-500/10 border-sky-400/20'],
        ['key' => 'wind', 'title' => 'Strongest Wind', 'field' => 'wind_speed', 'unit' => ' m/s', 'icon' => 'heroicon-o-bars-3-bottom-left', 'accent' => 'text-teal-300 bg-teal-500/10 border-teal-400/20'],
        ['key' => 'risk', 'title' => 'Highest Risk Cities', 'field' => 'risk_score', 'unit' => '/100', 'icon' => 'heroicon-o-shield-check', 'accent' => 'text-rose-300 bg-rose-500/10 border-rose-400/20'],
    ];
@endphp

<section aria-labelledby="leaderboard-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Latest City Snapshots</p>
            <h2 id="leaderboard-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Extreme Weather Leaderboard</h2>
        </div>
        <x-heroicon-o-trophy class="h-9 w-9 text-amber-300" />
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($boards as $board)
            <article class="rounded-2xl border border-white/10 bg-slate-950/25 p-5">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border {{ $board['accent'] }}">
                        <x-dynamic-component :component="$board['icon']" class="h-5 w-5" />
                    </div>
                    <h3 class="font-black text-white">{{ $board['title'] }}</h3>
                </div>

                <div class="space-y-2">
                    @forelse($leaderboards[$board['key']] as $rank => $weather)
                        <div class="flex items-center gap-3 rounded-xl bg-white/[.035] px-3 py-3">
                            <span class="flex h-7 w-7 flex-none items-center justify-center rounded-lg {{ $rank === 0 ? 'bg-amber-300 text-amber-950' : 'bg-white/5 text-slate-400' }} text-xs font-black">
                                #{{ $rank + 1 }}
                            </span>
                            <span class="min-w-0 flex-1 truncate text-sm font-semibold text-slate-300">{{ $weather->city }}</span>
                            <span class="text-sm font-black text-white">
                                {{ number_format($weather->{$board['field']}, $board['field'] === 'wind_speed' ? 1 : 0) }}{{ $board['unit'] }}
                            </span>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-white/10 px-3 py-8 text-center text-xs text-slate-600">No city data yet</p>
                    @endforelse
                </div>
            </article>
        @endforeach
    </div>
</section>
