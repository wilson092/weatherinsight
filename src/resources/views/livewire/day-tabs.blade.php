<div class="relative">
    <div class="flex snap-x snap-mandatory scroll-p-4 gap-3 overflow-x-auto pb-4 scrollbar-thin scrollbar-track-slate-900/50 scrollbar-thumb-slate-700/50">
        @if(!empty($forecast['daily']))
            @foreach(array_slice($forecast['daily'], 0, 7) as $index => $day)
                <div class="flex-shrink-0 snap-start">
                    <button
                        wire:click="selectDay({{ $index }})"
                        type="button"
                        class="flex w-36 flex-col items-center justify-center gap-2 rounded-2xl border p-3 text-sm transition-all duration-200
                            {{ $selectedDayIndex === $index
                                ? 'glass-panel border-cyan-400/60 bg-cyan-400/10 shadow-cyan-950/40'
                                : 'glass-panel border-transparent hover:border-slate-600 hover:bg-slate-800/50'
                            }}"
                    >
                        <span class="font-bold {{ $selectedDayIndex === $index ? 'text-white' : 'text-slate-300' }}">
                            {{ \Carbon\Carbon::createFromTimestamp($day['dt'])->format('D') }}
                        </span>
                        <img
                            src="https://openweathermap.org/img/wn/{{ $day['weather'][0]['icon'] }}@2x.png"
                            alt="{{ $day['weather'][0]['description'] }}"
                            class="-my-2 h-12 w-12"
                        >
                        <span class="font-semibold {{ $selectedDayIndex === $index ? 'text-white' : 'text-slate-400' }}">
                            {{ round($this->getConvertedTemperature($day['temp']['current'])) }}°
                        </span>
                        <span class="text-xs {{ $selectedDayIndex === $index ? 'text-white' : 'text-slate-400' }}">
                            Highest:{{ round($this->getConvertedTemperature($day['temp']['max'])) }}° Lowest:{{ round($this->getConvertedTemperature($day['temp']['min'])) }}°
                        </span>
                    </button>
                </div>
            @endforeach
        @else
            <div class="glass-panel flex w-full items-center justify-center rounded-2xl p-4 text-center">
                <p class="text-slate-400">Forecast data is not available.</p>
            </div>
        @endif
    </div>
</div>
