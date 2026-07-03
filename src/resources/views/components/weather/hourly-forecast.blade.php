@props(['forecast'])

@php
    // Extract hourly data (first 12 entries = 36 hours with 3-hour intervals)
    $hourlyData = array_slice($forecast['list'] ?? [], 0, 12);
    
    // Extract temperatures for chart visualization
    $temperatures = array_map(fn($item) => $item['main']['temp'], $hourlyData);
    $minTemp = !empty($temperatures) ? min($temperatures) : 0;
    $maxTemp = !empty($temperatures) ? max($temperatures) : 100;
    $tempRange = $maxTemp - $minTemp ?: 1;
@endphp

<section aria-labelledby="hourly-forecast-title" class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8">
    <!-- Background decorations -->
    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-teal-400/10 blur-3xl"></div>
    <div class="absolute -bottom-16 left-1/4 h-48 w-48 rounded-full bg-cyan-400/10 blur-3xl"></div>

    <div class="relative">
        <!-- Header -->
        <div class="mb-6">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Next 36 Hours</p>
            <h2 id="hourly-forecast-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Hourly Forecast</h2>
        </div>

        @if(!empty($hourlyData))
            <!-- Temperature Chart Visualization -->
            <div class="mb-6 rounded-2xl border border-white/10 bg-gradient-to-br from-slate-950/40 to-slate-900/20 p-6">
                <div class="relative h-32">
                    <!-- Chart Line (SVG) -->
                    <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="tempGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" style="stop-color:rgb(34, 211, 238);stop-opacity:0.3" />
                                <stop offset="100%" style="stop-color:rgb(34, 211, 238);stop-opacity:0.05" />
                            </linearGradient>
                        </defs>
                        
                        @php
                            $points = [];
                            $width = 100; // percentage
                            $height = 100; // percentage
                            $count = count($temperatures);
                            
                            foreach ($temperatures as $index => $temp) {
                                $x = ($index / ($count - 1)) * $width;
                                $y = $height - ((($temp - $minTemp) / $tempRange) * 80 + 10);
                                $points[] = "{$x},{$y}";
                            }
                            
                            $pathPoints = implode(' ', $points);
                            $areaPoints = $pathPoints . " 100,100 0,100";
                        @endphp
                        
                        <!-- Area under curve -->
                        <polygon points="{{ $areaPoints }}" fill="url(#tempGradient)" />
                        
                        <!-- Line -->
                        <polyline
                            points="{{ $pathPoints }}"
                            fill="none"
                            stroke="rgb(34, 211, 238)"
                            stroke-width="0.5"
                            vector-effect="non-scaling-stroke"
                        />
                    </svg>
                    
                    <!-- Temperature labels -->
                    <div class="absolute inset-0 flex items-end justify-between px-2 pb-2">
                        @foreach(array_slice($temperatures, 0, 5) as $temp)
                            <span class="text-xs font-bold text-cyan-300">{{ number_format($temp, 0) }}°</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Hourly Cards - Horizontal Scroll -->
            <div class="relative -mx-6 px-6 sm:-mx-8 sm:px-8">
                <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-white/10">
                    @foreach($hourlyData as $item)
                        @php
                            $time = \Carbon\Carbon::parse($item['dt_txt']);
                            $pop = round(($item['pop'] ?? 0) * 100); // Probability of precipitation
                        @endphp
                        
                        <article class="flex-none w-28 rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-4 text-center transition hover:border-cyan-300/30 hover:bg-white/[.08]">
                            <!-- Time -->
                            <p class="text-sm font-bold text-white">{{ $time->format('H:i') }}</p>
                            <p class="mt-0.5 text-[10px] uppercase tracking-wider text-slate-500">{{ $time->format('D') }}</p>
                            
                            <!-- Weather Icon -->
                            <img 
                                class="mx-auto my-3 h-12 w-12 drop-shadow-lg" 
                                src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}@2x.png" 
                                alt="{{ $item['weather'][0]['description'] }}"
                            >
                            
                            <!-- Chance of Rain -->
                            @if($pop > 0)
                                <div class="mb-2 flex items-center justify-center gap-1 text-[10px] text-cyan-300">
                                    <x-heroicon-o-beaker class="h-3 w-3" />
                                    <span>{{ $pop }}%</span>
                                </div>
                            @endif
                            
                            <!-- Temperature -->
                            <p class="text-2xl font-black text-white">{{ number_format($item['main']['temp'], 0) }}°</p>
                            
                            <!-- Description -->
                            <p class="mt-1 line-clamp-2 min-h-8 text-[10px] capitalize leading-tight text-slate-400">
                                {{ $item['weather'][0]['description'] }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-white/10 py-12 text-center text-sm text-slate-500">
                Hourly forecast data is currently unavailable.
            </div>
        @endif
    </div>
</section>