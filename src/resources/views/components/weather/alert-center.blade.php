@props(['alerts'])

<section aria-labelledby="alert-center-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Automated Monitoring</p>
            <h2 id="alert-center-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Weather Alert Center</h2>
        </div>
        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold text-slate-300">
            {{ count($alerts) }} active
        </span>
    </div>

    @if($alerts === [])
        <div class="flex items-start gap-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-5">
            <x-heroicon-o-check-circle class="h-8 w-8 flex-none text-emerald-300" />
            <div>
                <p class="font-bold text-emerald-200">No Active Weather Alerts</p>
                <p class="mt-1 text-sm leading-6 text-emerald-100/60">Current readings remain below all configured alert thresholds.</p>
            </div>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($alerts as $alert)
                @php
                    $alertTone = $alert['level'] === 'HIGH'
                        ? 'border-rose-400/25 bg-rose-500/10 text-rose-300'
                        : 'border-amber-400/25 bg-amber-500/10 text-amber-300';
                @endphp
                <article class="rounded-2xl border p-5 {{ $alertTone }}">
                    <div class="flex items-start gap-4">
                        <x-dynamic-component :component="$alert['icon']" class="h-8 w-8 flex-none" />
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-black text-white">{{ $alert['title'] }}</h3>
                                <span class="rounded-full bg-current/10 px-2 py-0.5 text-[10px] font-black tracking-widest">{{ $alert['level'] }}</span>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-300">{{ $alert['message'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
