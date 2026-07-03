@props(['history'])

<section id="history" aria-labelledby="history-title" class="glass-panel rounded-3xl p-6 sm:p-8">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Recorded Observations</p>
        <h2 id="history-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Weather History</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[850px] text-left">
            <thead>
                <tr class="border-b border-white/10 text-[11px] font-bold uppercase tracking-widest text-slate-500">
                    <th class="px-4 py-4">Recorded</th>
                    <th class="px-4 py-4">Temperature</th>
                    <th class="px-4 py-4">Humidity</th>
                    <th class="px-4 py-4">Pressure</th>
                    <th class="px-4 py-4">Wind</th>
                    <th class="px-4 py-4">Risk Score</th>
                    <th class="px-4 py-4">Level</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[.06]">
                @forelse($history as $item)
                    <tr class="transition hover:bg-white/[.035]">
                        <td class="px-4 py-4 text-sm font-semibold text-white">{{ $item->recorded_at?->format('d M, H:i') }}</td>
                        <td class="px-4 py-4 text-sm text-slate-300">{{ number_format($item->temperature, 1) }} °C</td>
                        <td class="px-4 py-4 text-sm text-slate-300">{{ number_format($item->humidity, 0) }}%</td>
                        <td class="px-4 py-4 text-sm text-slate-300">{{ number_format($item->pressure, 0) }} hPa</td>
                        <td class="px-4 py-4 text-sm text-slate-300">{{ number_format($item->wind_speed, 1) }} m/s</td>
                        <td class="px-4 py-4 text-sm font-black text-white">{{ $item->risk_score }}/100</td>
                        <td class="px-4 py-4">
                            <span class="rounded-full border px-3 py-1 text-[10px] font-black tracking-widest {{ $item->risk_level === 'HIGH' ? 'border-rose-400/20 bg-rose-500/10 text-rose-300' : ($item->risk_level === 'MEDIUM' ? 'border-amber-400/20 bg-amber-500/10 text-amber-300' : 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300') }}">
                                {{ $item->risk_level }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-14 text-center text-sm text-slate-600">No weather history is available for this city.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
