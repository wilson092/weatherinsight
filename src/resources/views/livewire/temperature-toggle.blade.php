<div
    wire:click="toggleUnit"
    class="flex h-10 cursor-pointer items-center justify-center rounded-full border border-white/10 bg-slate-900/70 p-1 text-sm font-bold text-slate-300 transition"
>
    <span
        class="flex h-8 w-12 items-center justify-center rounded-full transition"
        :class="{ 'bg-cyan-400/20 text-cyan-300': '{{ $unit }}' === 'C' }"
    >
        °C
    </span>
    <span
        class="flex h-8 w-12 items-center justify-center rounded-full transition"
        :class="{ 'bg-cyan-400/20 text-cyan-300': '{{ $unit }}' === 'F' }"
    >
        °F
    </span>
</div>
