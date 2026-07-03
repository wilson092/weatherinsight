@props(['latest', 'comparison', 'city'])

@php
    $secondary = data_get($comparison, 'weather');
    $comparisonError = data_get($comparison, 'error');
@endphp

<section id="comparison" aria-labelledby="comparison-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-6 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Side-by-side Intelligence</p>
            <h2 id="comparison-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Weather Comparison</h2>
        </div>

        <form method="GET" action="/" class="flex w-full flex-col gap-2 sm:flex-row lg:max-w-xl">
            <input type="hidden" name="city" value="{{ $city }}">
            <label class="relative flex-1">
                <span class="sr-only">Comparison city</span>
                <x-heroicon-o-map-pin class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" />
                <input
                    type="text"
                    name="compare_city"
                    value="{{ request('compare_city') }}"
                    placeholder="Compare with another city"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 py-3 pl-11 pr-4 text-sm text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                    required
                >
            </label>
            <button class="rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:bg-cyan-200" type="submit">
                Compare City
            </button>
        </form>
    </div>

    @if($comparisonError)
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4 text-sm text-rose-200">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-none" />
            {{ $comparisonError }}
        </div>
    @endif

    <div class="grid items-stretch gap-4 lg:grid-cols-[1fr_auto_1fr]">
        <x-weather.comparison-card :weather="$latest" label="Primary City" source="live" />

        <div class="flex items-center justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full border border-cyan-300/20 bg-cyan-300/10 text-sm font-black text-cyan-200 shadow-lg shadow-cyan-950/30">
                VS
            </div>
        </div>

        <x-weather.comparison-card
            :weather="$secondary"
            label="Comparison City"
            :source="data_get($comparison, 'source')"
        />
    </div>
</section>
